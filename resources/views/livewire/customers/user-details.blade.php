<?php

use App\Models\SavingsTransaction;
use App\Models\FdTransaction;
use App\Models\Customer;
use Livewire\Volt\Component;

new class extends Component {
    public Customer $customer;
    public $savingsAccounts = [];
    public $fixedDeposits = [];
    public $totalSavingsBalance = 0;
    public $totalFdPrincipal = 0;
    public $totalFdMaturity = 0;
    public $savingsAccountIds = [];
    public $fdIds = [];
    public $savingsTransactions = [];
    public $fdTransactions = [];
    public $allTransactions = [];

    public function mount(): void
    {
        $this->customer->load(['branch', 'status', 'savingsAccounts.accountType', 'fixedDeposits.fdType', 'fixedDeposits.status']);
        $this->initializeData();
    }

    public function initializeData(): void
    {
        $this->savingsAccounts = $this->customer->savingsAccounts;
        $this->fixedDeposits = $this->customer->fixedDeposits;
        
        $this->calculateTotals();
        $this->extractAccountIds();
        $this->loadTransactions();
    }

    private function calculateTotals(): void
    {
        $this->totalSavingsBalance = $this->savingsAccounts->sum('balance');
        $this->totalFdPrincipal = $this->fixedDeposits->sum('principal_amount');
        $this->totalFdMaturity = $this->fixedDeposits->sum('maturity_amount');
    }

    private function extractAccountIds(): void
    {
        $this->savingsAccountIds = $this->savingsAccounts->pluck('id')->toArray();
        $this->fdIds = $this->fixedDeposits->pluck('id')->toArray();
    }

    private function loadTransactions(): void
    {
        $this->savingsTransactions = SavingsTransaction::where(function ($query) {
            $query->whereIn('from_id', $this->savingsAccountIds)
                  ->orWhereIn('to_id', $this->savingsAccountIds);
        })->with(['fromAccount', 'toAccount'])->orderBy('created_at', 'desc')->take(50)->get();

        $this->fdTransactions = FdTransaction::whereIn('fd_acc_id', $this->fdIds)
            ->with(['fixedDeposit'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        $this->allTransactions = $this->savingsTransactions
            ->concat($this->fdTransactions)
            ->sortByDesc('created_at')
            ->take(50);
    }

    public function getTransactionHeaders(): array
    {
        return [
            ['key' => 'date', 'label' => 'Date'],
            ['key' => 'type', 'label' => 'Type'],
            ['key' => 'account', 'label' => 'Account'],
            ['key' => 'amount', 'label' => 'Amount'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'description', 'label' => 'Description'],
        ];
    }

    public function getTransactionType($transaction): array
    {
        if ($transaction instanceof SavingsTransaction) {
            return match ($transaction->type?->value) {
                'DEPOSIT' => [
                    'badge' => 'badge-success',
                    'icon' => 'o-arrow-down',
                    'label' => $transaction->type?->label() ?? 'Deposit'
                ],
                'WITHDRAWAL' => [
                    'badge' => 'badge-error',
                    'icon' => 'o-arrow-up',
                    'label' => $transaction->type?->label() ?? 'Withdrawal'
                ],
                default => [
                    'badge' => 'badge-info',
                    'icon' => 'o-arrow-path',
                    'label' => $transaction->type?->label() ?? 'Transfer'
                ]
            };
        }

        return [
            'badge' => 'badge-warning',
            'icon' => 'o-banknotes',
            'label' => 'FD ' . ($transaction->type?->value ?? 'Transaction')
        ];
    }

    public function getTransactionAccount($transaction): string
    {
        if ($transaction instanceof SavingsTransaction) {
            if ($transaction->from_id && in_array($transaction->from_id, $this->savingsAccountIds)) {
                return $transaction->fromAccount?->account_number ?? 'N/A';
            }

            if ($transaction->to_id && in_array($transaction->to_id, $this->savingsAccountIds)) {
                return $transaction->toAccount?->account_number ?? 'N/A';
            }
        }

        return $transaction->fixedDeposit?->fd_number ?? 'N/A';
    }

    public function getTransactionStatus($transaction): array
    {
        if ($transaction instanceof SavingsTransaction) {
            return match ($transaction->status?->value) {
                'COMPLETED' => [
                    'badge' => 'badge-success',
                    'label' => $transaction->status?->label() ?? 'Completed'
                ],
                'PENDING' => [
                    'badge' => 'badge-warning',
                    'label' => $transaction->status?->label() ?? 'Pending'
                ],
                default => [
                    'badge' => 'badge-error',
                    'label' => $transaction->status?->label() ?? 'Failed'
                ]
            };
        }

        return [
            'badge' => 'badge-success',
            'label' => 'Completed'
        ];
    }
};
?>

<div class="space-y-6">
    {{-- Back Button --}}
    <div class="mb-4">
        <x-mary-button
            label="Back to Customers"
            icon="o-arrow-left"
            link="{{ route('customers.view') }}"
            class="btn-ghost btn-sm" />
    </div>

    {{-- Page Header --}}
    <x-mary-header
        title="Customer Details"
        subtitle="Customer ID: {{ $customer->id }}"
        separator />

    {{-- Customer Information Cards --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {{-- Personal Info Card --}}
        <x-mary-card shadow>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary/10 rounded-lg">
                    <x-mary-icon name="o-user" class="w-8 h-8 text-primary" />
                </div>
                <div>
                    <div class="text-sm text-gray-500">Full Name</div>
                    <div class="text-lg font-bold">{{ $customer->first_name . ' ' . $customer->last_name }}</div>
                </div>
            </div>
        </x-mary-card>

        {{-- Contact Card --}}
        <x-mary-card shadow>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-info/10 rounded-lg">
                    <x-mary-icon name="o-phone" class="w-8 h-8 text-info" />
                </div>
                <div>
                    <div class="text-sm text-gray-500">Phone</div>
                    <div class="text-lg font-semibold">{{ $customer->phone }}</div>
                    <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                </div>
            </div>
        </x-mary-card>

        {{-- Status Card --}}
        <x-mary-card shadow>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-success/10 rounded-lg">
                    <x-mary-icon name="o-flag" class="w-8 h-8 text-success" />
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status</div>
                    <div class="mt-1">
                        <span class="badge badge-success badge-lg">{{ $customer->status->status_name ?? 'Active' }}</span>
                    </div>
                </div>
            </div>
        </x-mary-card>
    </div>

    {{-- Detailed Information --}}
    <div class="grid gap-6 md:grid-cols-2">
        {{-- Customer Details --}}
        <x-mary-card title="Customer Information" shadow separator>
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Date of Birth</span>
                    <span class="font-semibold">{{ $customer->date_of_birth?->format('F d, Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Gender</span>
                    <span class="font-semibold">{{ $customer->gender->label() }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">ID Type</span>
                    <span class="font-semibold">{{ $customer->id_type }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">ID Number</span>
                    <span class="font-mono font-semibold">{{ $customer->id_number }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Address</span>
                    <span class="font-semibold">{{ $customer->address }}, {{ $customer->city }}, {{ $customer->state }} {{ $customer->postal_code }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Branch</span>
                    <span class="font-semibold">{{ $customer->branch->branch_name }}</span>
                </div>
            </div>
        </x-mary-card>

        {{-- Account Summary --}}
        <x-mary-card title="Account Summary" shadow separator>
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Savings Accounts</span>
                    <span class="font-semibold">{{ count($savingsAccounts) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Total Savings Balance</span>
                    <span class="font-semibold text-success">Rs. {{ number_format($totalSavingsBalance, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Fixed Deposits</span>
                    <span class="font-semibold">{{ count($fixedDeposits) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Total FD Principal</span>
                    <span class="font-semibold text-info">Rs. {{ number_format($totalFdPrincipal, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Total FD Maturity</span>
                    <span class="font-semibold text-warning">Rs. {{ number_format($totalFdMaturity, 2) }}</span>
                </div>
            </div>
        </x-mary-card>
    </div>

    {{-- Savings Accounts Section --}}
    @if(count($savingsAccounts) > 0)
        <x-mary-card title="Savings Accounts" shadow separator>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($savingsAccounts as $account)
                    <x-mary-card shadow class="border">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">{{ $account->account_number }}</div>
                                <div class="text-sm text-gray-500">{{ $account->accountType->name }}</div>
                                <div class="text-lg font-bold text-success">Rs. {{ number_format($account->balance, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $account->status === 'ACTIVE' ? 'badge-success' : 'badge-warning' }} badge-sm">
                                    {{ $account->status }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Opened: {{ $account->opened_date?->format('M d, Y') }}
                        </div>
                    </x-mary-card>
                @endforeach
            </div>
        </x-mary-card>
    @endif

    {{-- Fixed Deposits Section --}}
    @if(count($fixedDeposits) > 0)
        <x-mary-card title="Fixed Deposits" shadow separator>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($fixedDeposits as $fd)
                    <x-mary-card shadow class="border">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">{{ $fd->fd_number }}</div>
                                <div class="text-sm text-gray-500">{{ $fd->fdType->name }}</div>
                                <div class="text-lg font-bold text-info">Principal: Rs. {{ number_format($fd->principal_amount, 2) }}</div>
                                <div class="text-sm text-warning">Maturity: Rs. {{ number_format($fd->maturity_amount, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $fd->status->value === 'ACTIVE' ? 'badge-success' : 'badge-warning' }} badge-sm">
                                    {{ $fd->status->label() }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500">
                            Start: {{ $fd->start_date?->format('M d, Y') }} | Maturity: {{ $fd->maturity_date?->format('M d, Y') }}
                        </div>
                    </x-mary-card>
                @endforeach
            </div>
        </x-mary-card>
    @endif

    {{-- Transaction History --}}
    <x-mary-card title="Transaction History" shadow separator>
        @if(count($allTransactions) === 0)
            <div class="text-center py-12">
                <x-mary-icon name="o-document-text" class="w-16 h-16 mx-auto text-gray-300" />
                <p class="mt-4 text-gray-500">No transactions found for this customer.</p>
            </div>
        @else
            <x-mary-table :headers="$this->getTransactionHeaders()" :rows="$allTransactions">
                @scope('cell_date', $transaction)
                    <div class="text-sm">{{ $transaction->created_at?->format('M d, Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $transaction->created_at?->format('h:i A') }}</div>
                @endscope

                @scope('cell_type', $transaction)
                    @php $typeData = $this->getTransactionType($transaction); @endphp
                    <span class="badge badge-soft {{ $typeData['badge'] }} badge-sm">
                        <x-mary-icon :name="$typeData['icon']" class="w-3 h-3 mr-1" />
                        {{ $typeData['label'] }}
                    </span>
                @endscope

                @scope('cell_account', $transaction)
                    <span class="font-mono text-sm">{{ $this->getTransactionAccount($transaction) }}</span>
                @endscope

                @scope('cell_amount', $transaction)
                    <span class="font-semibold">
                        Rs. {{ number_format((float)$transaction->amount, 2) }}
                    </span>
                @endscope

                @scope('cell_status', $transaction)
                    @php $statusData = $this->getTransactionStatus($transaction); @endphp
                    <span class="badge badge-soft {{ $statusData['badge'] }} badge-sm">
                        {{ $statusData['label'] }}
                    </span>
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