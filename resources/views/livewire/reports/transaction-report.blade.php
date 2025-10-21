<?php

use App\Models\SavingsTransaction;
use App\Models\SavingsAccount;
use Livewire\Volt\Component;

new class extends Component {
    public $search = '';
    public $perPage = 10;
    public $selectedAccount = null;

    public function mount(): void
    {
        // No initial data loading needed for Volt
    }

    public function selectAccount($accountNumber)
    {
        $this->selectedAccount = SavingsAccount::where('account_number', $accountNumber)->first();
    }

    public function with(): array
    {
        // Get matching accounts
        $accounts = SavingsAccount::with(['customers', 'accountType'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('account_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customers', function ($customerQuery) {
                          $customerQuery->where('first_name', 'like', '%' . $this->search . '%')
                                      ->orWhere('last_name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('account_number')
            ->get();

        // Get transactions for selected account
        $transactions = collect([]);
        if ($this->selectedAccount) {
            $transactions = SavingsTransaction::with(['fromAccount', 'toAccount'])
                ->where(function($query) {
                    $query->where('from_id', $this->selectedAccount->id)
                          ->orWhere('to_id', $this->selectedAccount->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage);
        }

        return [
            'accounts' => $accounts,
            'transactions' => $transactions,
        ];
    }
}; ?>

<div class="space-y-6">
    {{-- Search Form --}}
    <x-mary-card shadow>
        <div class="flex gap-4">
            <div class="flex-1">
                <x-mary-input
                    wire:model.live.debounce.300ms="search"
                    label="Account Number"
                    placeholder="Search by account number or customer name"
                    icon="o-magnifying-glass" />
            </div>
            <div class="flex items-end">
                <x-mary-select
                    wire:model.live="perPage"
                    :options="[['id' => 10, 'name' => '10 per page'], ['id' => 25, 'name' => '25 per page'], ['id' => 50, 'name' => '50 per page']]"
                    placeholder="Items per page" />
            </div>
        </div>
    </x-mary-card>

    {{-- Matching Accounts --}}
    @if($search)
        @if($accounts->isNotEmpty())
            <x-mary-card title="Matching Accounts" shadow>
                <div class="space-y-4">
                    @foreach($accounts as $account)
                        <div class="p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-mono text-lg font-semibold">{{ $account->account_number }}</div>
                                    <div class="text-sm text-gray-500">
                                        Customers: 
                                        @foreach($account->customers as $customer)
                                            {{ $customer->first_name }} {{ $customer->last_name }}@if(!$loop->last), @endif
                                        @endforeach
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge badge-{{ $account->status === 'ACTIVE' ? 'success' : 'warning' }}">
                                            {{ $account->status }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-semibold text-success">
                                        Rs. {{ number_format((float)$account->balance, 2) }}
                                    </div>
                                    <div class="mt-2">
                                        <button wire:click="selectAccount('{{ $account->account_number }}')" 
                                                class="btn btn-primary btn-sm">
                                            View Transactions
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-mary-card>
        @else
            <x-mary-card class="bg-warning/10">
                <div class="text-center py-6">
                    <x-mary-icon name="o-exclamation-triangle" class="w-12 h-12 mx-auto text-warning mb-4" />
                    <p class="text-lg font-semibold">No Matching Accounts</p>
                    <p class="text-gray-500">No accounts found matching "{{ $search }}"</p>
                </div>
            </x-mary-card>
        @endif
    @endif

    {{-- Selected Account Details --}}
    @if($selectedAccount)
        <x-mary-card title="Account Information" shadow class="mb-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <div class="text-sm text-gray-500">Account Number</div>
                    <div class="font-mono text-lg font-semibold">{{ $selectedAccount->account_number }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Current Balance</div>
                    <div class="text-lg font-semibold text-success">Rs. {{ number_format($selectedAccount->balance, 2) }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status</div>
                    <div>
                        <span class="badge badge-{{ $selectedAccount->status === 'ACTIVE' ? 'success' : 'warning' }}">
                            {{ $selectedAccount->status }}
                        </span>
                    </div>
                </div>
            </div>
        </x-mary-card>

        {{-- Transactions Table --}}
        <x-mary-card title="Account Transactions" shadow separator>
            @if($transactions->isEmpty())
                <div class="text-center py-12">
                    <x-mary-icon name="o-document-text" class="w-16 h-16 mx-auto text-gray-300" />
                    <p class="mt-4 text-gray-500">No transactions found for this account.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <div class="text-sm">{{ $transaction->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        @if($transaction->type?->value === 'DEPOSIT')
                                            <span class="badge badge-soft badge-success badge-sm">
                                                <x-mary-icon name="o-arrow-down" class="w-3 h-3 mr-1" />
                                                {{ $transaction->type?->name }}
                                            </span>
                                        @elseif($transaction->type?->value === 'WITHDRAWAL')
                                            <span class="badge badge-soft badge-error badge-sm">
                                                <x-mary-icon name="o-arrow-up" class="w-3 h-3 mr-1" />
                                                {{ $transaction->type?->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-soft badge-info badge-sm">
                                                <x-mary-icon name="o-arrow-path" class="w-3 h-3 mr-1" />
                                                {{ $transaction->type?->name }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="space-y-1">
                                            @if($transaction->type?->value === 'TRANSFER')
                                                @if($transaction->from_id == $selectedAccount->id)
                                                    <div class="text-sm">Transfer to: 
                                                        <span class="font-mono">{{ $transaction->toAccount?->account_number }}</span>
                                                    </div>
                                                @else
                                                    <div class="text-sm">Transfer from: 
                                                        <span class="font-mono">{{ $transaction->fromAccount?->account_number }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                            <div class="text-xs text-gray-500">{{ $transaction->description ?? '-' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $isCredit = $transaction->to_id == $selectedAccount->id;
                                        @endphp
                                        <span class="font-semibold {{ $isCredit ? 'text-success' : 'text-error' }}">
                                            {{ $isCredit ? '+' : '-' }} Rs. {{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-mono text-sm">
                                            Rs. {{ number_format((float)($transaction->balance_after ?? 0), 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft badge-{{ $transaction->status?->value === 'COMPLETED' ? 'success' : ($transaction->status?->value === 'PENDING' ? 'warning' : 'error') }} badge-sm">
                                            {{ $transaction->status?->name ?? 'Unknown' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </x-mary-card>

        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Transactions"
                :value="$transactions->total()"
                icon="o-credit-card"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            @php
                $totalDeposits = $transactions->where('to_id', $selectedAccount->id)->sum('amount');
                $totalWithdrawals = $transactions->where('from_id', $selectedAccount->id)->sum('amount');
            @endphp

            <x-mary-stat
                title="Total Deposits"
                :value="'Rs. ' . number_format($totalDeposits, 2)"
                icon="o-arrow-down"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Withdrawals"
                :value="'Rs. ' . number_format($totalWithdrawals, 2)"
                icon="o-arrow-up"
                color="text-error"
                class="bg-base-100 shadow-md" />
        </div>
    @endif
</div>