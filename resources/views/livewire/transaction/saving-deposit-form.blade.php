<?php

use Livewire\Volt\Component;
use App\Models\SavingsTransaction;
use App\Models\SavingsAccount;

new class extends Component {
    public $full_name;
    public $email;
    public $phone;
    public $nic_number;
    public $account_number;        
    public $amount;
    public $description;
    

    public $accounts = [];

    protected $rules = [
        'account_number' => 'nullable|exists:savings_account,account_number',
        'amount' => 'required|numeric|min:1',
        'description' => 'nullable|string|max:255',
        'full_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'nic_number' => 'nullable|string|max:20',
    ];


    public function mount()
    {
        $this->accounts = SavingsAccount::all();
    }


    public function submit()
    {
        $this->validate();

        $toAccount = SavingsAccount::where('account_number', $this->account_number)->first();
        
        $balanceBefore = $toAccount->balance;
        $balanceAfter = $balanceBefore + $this->amount;

        $toAccount->balance = $balanceAfter;
        $toAccount->last_transaction_date = now();
        $toAccount->save();


        SavingsTransaction::create([
            'type' => 'DEPOSIT',
            'from_id' => null,
            'to_id' => $toAccount->id,
            'amount' => $this->amount,
            'status' => 'COMPLETED',
            'description' => $this->description,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ]);

        session()->flash('message', 'Transaction done successfully.');

        $this->reset([
            'full_name',
            'email',
            'phone',
            'nic_number',
            'account_number',
            'amount',
            'description',
        ]);
        $this->mount(); 
    }
  
};
?>



<!--Message-->
@if (session()->has('error'))
    <div class="bg-red-500 text-red-1000 px-4 py-2 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

@if (session()->has('message'))
    <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('message') }}
    </div>
@endif

<!--Form-->
<x-mary-form wire:submit="submit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <x-mary-input label="Full Name" wire:model="full_name" required class="w-full" />
        <x-mary-input label="Email" wire:model="email" type="email" required class="w-full" />
        <x-mary-input label="Phone Number" wire:model="phone" required class="w-full" />
        <x-mary-input label="Account Number" wire:model="account_number" required class="w-full" />
        <x-mary-input label="NIC Number" wire:model="nic_number" required class="w-full" />
        <x-mary-input label="Amount (Rs.)" wire:model="amount" type="number" min="0" required class="w-full" />
        <div class="col-span-1 md:col-span-2">
            <x-mary-textarea label="Description" wire:model="description" rows="2" class="w-full" />
        </div>

    </div>

    <!-- Submit Button -->
    <x-slot:actions>
        <div class="flex justify-center mt-6 w-full">
            <x-mary-button label="Transfer Money"
                class="w-full py-3 font-semibold rounded"
                type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>
