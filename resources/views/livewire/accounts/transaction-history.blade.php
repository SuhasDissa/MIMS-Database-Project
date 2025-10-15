<?php

use App\Models\SavingsTransaction;
use App\Models\SavingsAccount;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public SavingsAccount $account;

    public function with(): array
    {
        // Get transactions where this account is either sender or receiver
        $transactions = SavingsTransaction::where(function ($query) {
                $query->where('from_id', $this->account->id)
                      ->orWhere('to_id', $this->account->id);
            })
            ->with(['fromAccount', 'toAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return [
            'transactions' => $transactions,
            'accountId' => $this->account->id,
        ];
    }
}; ?>

<div>
    <x-mary-card title="Transaction History" shadow separator>
        @if($transactions->isEmpty())
            <div class="text-center py-12">
                <x-mary-icon name="o-document-text" class="w-16 h-16 mx-auto text-gray-300" />
                <p class="mt-4 text-gray-500">No transactions found for this account.</p>
            </div>
        @else
            @php
                $headers = [
                    ['key' => 'date', 'label' => 'Date'],
                    ['key' => 'type', 'label' => 'Type'],
                    ['key' => 'from_account', 'label' => 'From Account'],
                    ['key' => 'to_account', 'label' => 'To Account'],
                    ['key' => 'amount', 'label' => 'Amount'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'description', 'label' => 'Description'],
                ];
            @endphp

            <x-mary-table :headers="$headers" :rows="$transactions" with-pagination>
                @scope('cell_date', $transaction)
                    <div class="text-sm">{{ $transaction->created_at->format('M d, Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $transaction->created_at->format('h:i A') }}</div>
                @endscope

                @scope('cell_type', $transaction)
                    @if($transaction->type === 'DEPOSIT')
                        <span class="badge badge-soft badge-success badge-sm">
                            <x-mary-icon name="o-arrow-down" class="w-3 h-3 mr-1" />
                            {{ $transaction->type->name }}
                        </span>
                    @elseif($transaction->type === 'WITHDRAWAL')
                        <span class="badge badge-soft badge-error badge-sm">
                            <x-mary-icon name="o-arrow-up" class="w-3 h-3 mr-1" />
                            {{ $transaction->type->name }}
                        </span>
                    @else
                        <span class="badge badge-soft badge-info badge-sm">
                            <x-mary-icon name="o-arrow-path" class="w-3 h-3 mr-1" />
                            {{ $transaction->type->name }}
                        </span>
                    @endif
                @endscope

                @scope('cell_from_account', $transaction)
                    @if($transaction->from_id)
                        <a href="{{ route('accounts.details', $transaction->from_id) }}"
                           class="font-mono text-sm link link-hover {{ $transaction->from_id == $this->account->id ? 'font-bold text-primary' : 'text-blue-600' }}">
                            {{ $transaction->fromAccount->account_number }}
                        </a>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                @endscope

                @scope('cell_to_account', $transaction)
                    @if($transaction->to_id)
                        <a href="{{ route('accounts.details', $transaction->to_id) }}"
                           class="font-mono text-sm link link-hover {{ $transaction->to_id == $this->account->id ? 'font-bold text-primary' : 'text-blue-600' }}">
                            {{ $transaction->toAccount->account_number }}
                        </a>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                @endscope

                @scope('cell_amount', $transaction)
                    @php
                        $isCredit = $transaction->to_id == $this->account->id;
                        $isDebit = $transaction->from_id == $this->account->id;
                    @endphp
                    <span class="font-semibold {{ $isCredit ? 'text-success' : 'text-error' }}">
                        {{ $isCredit ? '+' : '-' }} Rs. {{ number_format($transaction->amount, 2) }}
                    </span>
                @endscope

                @scope('cell_status', $transaction)
                    @if($transaction->status === 'COMPLETED')
                        <span class="badge badge-soft badge-success badge-sm">{{ $transaction->status->name }}</span>
                    @elseif($transaction->status === 'PENDING')
                        <span class="badge badge-soft badge-warning badge-sm">{{ $transaction->status->name }}</span>
                    @else
                        <span class="badge badge-soft badge-error badge-sm">{{ $transaction->status->name }}</span>
                    @endif
                @endscope

                @scope('cell_description', $transaction)
                    <div class="text-sm max-w-xs truncate" title="{{ $transaction->description }}">
                        {{ $transaction->description ?? '-' }}
                    </div>
                @endscope
            </x-mary-table>
        @endif
    </x-mary-card>
</div>
