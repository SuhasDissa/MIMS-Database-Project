@php
    use App\Models\SavingsTransaction;
    use App\Models\SavingsAccount;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Transaction Report" subtitle="Search transactions by account number" separator />
        </div>

        {{-- Search Form --}}
        <x-mary-card title="Search Account" shadow>
            <form method="GET" class="space-y-4">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <x-mary-input
                            name="account_number"
                            label="Account Number"
                            placeholder="Enter account number"
                            value="{{ request('account_number') }}"
                        />
                    </div>
                    <div class="flex items-end">
                        <x-mary-button type="submit" label="Search" icon="o-magnifying-glass" />
                    </div>
                </div>
            </form>
        </x-mary-card>

        @php
            $accountNumber = request('account_number');
            $account = null;
            $transactions = collect([]);

            if ($accountNumber) {
                $account =SavingsAccount::with(['customers', 'accountType'])
                ->where('account_number', 'like', '%' . $accountNumber . '%')
                ->orWhereHas('customers', function($query) use ($accountNumber) {
                    $query->where('first_name', 'like', '%' . $accountNumber . '%')
                          ->orWhere('last_name', 'like', '%' . $accountNumber . '%');
                })->first();
                if ($account) {
                    $transactions = SavingsTransaction::with(['fromAccount', 'toAccount'])
                        ->where(function($query) use ($account) {
                            $query->where('from_id', $account->id)
                                  ->orWhere('to_id', $account->id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(25);
                }
            }
        @endphp

        @if(request('account_number'))
            @if($account)
                {{-- Account Info --}}
                <x-mary-card title="Account Information" shadow class="mb-6">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <div class="text-sm text-gray-500">Account Number</div>
                            <div class="font-mono text-lg font-semibold">{{ $account->account_number }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Current Balance</div>
                            <div class="text-lg font-semibold text-success">Rs. {{ number_format($account->balance, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Status</div>
                            <div>
                                <span class="badge badge-{{ $account->status === 'ACTIVE' ? 'success' : 'warning' }}">
                                    {{ $account->status }}
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
                                                    @if($transaction->from_id == $account->id)
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
                                                $isCredit = $transaction->to_id == $account->id;
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

                        <div class="mt-4">
                            {{ $transactions->appends(['account_number' => request('account_number')])->links() }}
                        </div>
                    @endif
                </x-mary-card>
            @else
                <x-mary-card class="bg-warning/10">
                    <div class="text-center py-6">
                        <x-mary-icon name="o-exclamation-triangle" class="w-12 h-12 mx-auto text-warning mb-4" />
                        <p class="text-lg font-semibold">Account Not Found</p>
                        <p class="text-gray-500">No account found with the number "{{ request('account_number') }}"</p>
                    </div>
                </x-mary-card>
            @endif
        @else
            <x-mary-card class="bg-info/10">
                <div class="text-center py-12">
                    <x-mary-icon name="o-magnifying-glass" class="w-16 h-16 mx-auto text-info mb-4" />
                    <p class="text-lg font-semibold">Enter Account Number</p>
                    <p class="text-gray-500">Enter an account number above to view its transaction history</p>
                </div>
            </x-mary-card>
        @endif

        @if($account)
            {{-- Summary Statistics for the Account --}}
            <div class="grid gap-4 md:grid-cols-3">
                <x-mary-stat
                    title="Total Transactions"
                    :value="$transactions->total()"
                    icon="o-credit-card"
                    color="text-primary"
                    class="bg-base-100 shadow-md" />

                @php
                    $totalDeposits = $transactions->where('to_id', $account->id)->sum('amount');
                    $totalWithdrawals = $transactions->where('from_id', $account->id)->sum('amount');
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
</x-layouts.app>