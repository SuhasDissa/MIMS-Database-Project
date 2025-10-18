<?php

use Livewire\Volt\Component;
use App\Models\Customer;
use App\Models\SavingsAccount;
use App\Models\FixedDeposit;
use App\Models\SavingsTransaction;
use App\Models\FdTransaction;
use App\Models\savings_accounts_customers;

new class extends Component {
    public $nic_number = '';
    public $customer = null;
    public $accounts = [];
    public $transactions = [];

   public function search()
{
    $this->customer = Customer::where('id_number', $this->nic_number)->first();

    if (!$this->customer) {
        $this->reset(['accounts', 'fdAccounts', 'transactions']);
        session()->flash('error', 'Customer not found.');
        return;
    }

    // Fetch savings accounts directly
    $this->accounts = SavingsAccount::where('customer_id', $this->customer->id)->get();

    // Fetch FDs linked to these accounts
    $this->fdAccounts = FixedDeposit::whereIn('linked_account_id', $this->accounts->pluck('id'))->get();

    // Fetch transactions
    $savingsTxns = SavingsTransaction::whereIn('account_id', $this->accounts->pluck('id'))->get();
    $fdTxns = FdTransaction::whereIn('fd_account_id', $this->fdAccounts->pluck('id'))->get();

    $savingsTxns->each(fn($t) => $t->type = 'Savings');
    $fdTxns->each(fn($t) => $t->type = 'FD');

    $this->transactions = $savingsTxns->merge($fdTxns)
        ->sortByDesc('created_at')
        ->take(10)
        ->values();
}

};
?>

<div class="space-y-6">

    <!-- Search Box -->
    <x-mary-card class="bg-white shadow-lg p-6 w-full">
    <div class="flex space-x-4 items-end w-full">
        <x-mary-input 
            label="Enter NIC Number" 
            wire:model="nic_number" 
            placeholder="e.g. 200045678912" 
            class="w-full" required 
        />
        <x-mary-button 
            label="Search" 
            icon="o-magnifying-glass" 
            class="btn-primary w-[160px] h-[42px]" 
            type="button"
            wire:click="search"
        />
    </div>

    @if (session('error'))
        <div class="text-red-500 mt-2 font-semibold">{{ session('error') }}</div>
    @endif
</x-mary-card>

    @if ($customer)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

            <!-- Customer Details -->
            <x-mary-card class="md:col-span-1 bg-white shadow-lg p-">
                <h2 class="font-bold text-xl mb-4 border-b pb-2">Customer Details</h2>
                <div class="space-y-2 text-gray-700 text-sm">
                    <p><span class="font-semibold">Name:</span> {{ $customer->first_name }} {{ $customer->last_name }}</p>
                    <p><span class="font-semibold">NIC:</span> {{ $customer->id_number }}</p>
                    <p><span class="font-semibold">Email:</span> {{ $customer->email }}</p>
                    <p><span class="font-semibold">Phone:</span> {{ $customer->phone }}</p>
                    <p><span class="font-semibold">Status:</span> {{ $customer->status->status_name ?? 'N/A' }}</p>
                    <p><span class="font-semibold">Branch:</span> {{ $customer->branch->branch_name ?? 'N/A' }}</p>
                </div>
            </x-mary-card>

            <!-- Accounts Overview -->
            <x-mary-card class="md:col-span-3 bg-white shadow-lg p-6">
                <h2 class="font-bold text-xl mb-4 border-b pb-2">Accounts Overview</h2>

                <div class="space-y-4">
                    @foreach ($accounts as $account)
                        <div class="p-4 border rounded-lg flex justify-between items-center hover:shadow-md transition-shadow duration-200">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $account->account_number }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($account->account_type) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-lg text-green-600">Rs. {{ number_format($account->balance, 2) }}</p>
                                <p class="text-xs text-gray-400">Balance</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-mary-card>

        </div>

        <!-- Recent Transactions -->
        <x-mary-card class="mt-6 bg-white shadow-lg p-6">
            <h2 class="font-bold text-xl mb-4 border-b pb-2">Recent Transactions</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-3 text-left text-gray-600 font-medium">Date</th>
                            <th class="py-2 px-3 text-left text-gray-600 font-medium">Account</th>
                            <th class="py-2 px-3 text-left text-gray-600 font-medium">Type</th>
                            <th class="py-2 px-3 text-right text-gray-600 font-medium">Amount</th>
                            <th class="py-2 px-3 text-left text-gray-600 font-medium">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($transactions as $txn)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-2 px-3">{{ $txn->created_at->format('Y-m-d') }}</td>
                                <td class="py-2 px-3">{{ $txn->account->account_number ?? 'N/A' }}</td>
                                <td class="py-2 px-3">{{ ucfirst($txn->type) }}</td>
                                <td class="py-2 px-3 text-right text-green-600">Rs. {{ number_format($txn->amount, 2) }}</td>
                                <td class="py-2 px-3">{{ $txn->description ?? '-' }}</td>
                            </tr>
                        @endforeach
                        @if(count($transactions) == 0)
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">No transactions found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </x-mary-card>
    @endif

</div>
