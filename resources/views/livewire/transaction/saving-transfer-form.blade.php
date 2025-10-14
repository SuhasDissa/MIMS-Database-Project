<?php

use Livewire\Volt\Component;
use App\Models\SavingsTransaction;
use App\Models\SavingsAccount;

new class extends Component {
    public $sender_name;
    public $receiver_name;
    public $email;
    public $phone;
    public $nic_number;
    public $from_num;         
    public $to_num;          
    public $amount;
    public $description;
    

    public $accounts = [];

    protected $rules = [
        'from_num' => 'nullable|exists:savings_account,account_number',
        'to_num' => 'nullable|exists:savings_account,account_number|different:from_num',
        'amount' => 'required|numeric|min:1',
        'description' => 'nullable|string|max:255',
        'sender_name' => 'nullable|string|max:255',
        'receiver_name' => 'nullable|string|max:255',
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

        $fromAccount = SavingsAccount::where('account_number', $this->from_num)->first();
        $toAccount = SavingsAccount::where('account_number', $this->to_num)->first();

        if (!$fromAccount || !$toAccount) {
            session()->flash('error', 'Invalid accounts provided.');
            return;
        }

        $fromBalanceBefore = $fromAccount->balance;
        $toBalanceBefore = $toAccount->balance;

        if ($fromBalanceBefore < $this->amount) {
            session()->flash('error', 'Insufficient balance.');
            return;
        }

        $fromAccount->update([
            'balance' => $fromBalanceBefore - $this->amount,
            'last_transaction_date' => now(),
        ]);

        $toAccount->update([
            'balance' => $toBalanceBefore + $this->amount,
            'last_transaction_date' => now(),
        ]);


        SavingsTransaction::create([
            'type' => 'TRANSFER',
            'from_id' => $fromAccount->id,
            'to_id' => $toAccount->id,
            'amount' => $this->amount,
            'status' => 'COMPLETED',
            'description' => $this->description,
            'balance_before' => $fromBalanceBefore,
            'balance_after' => $fromBalanceBefore - $this->amount,
        ]);

        session()->flash('message', 'Transaction done successfully.');

        $this->reset([
            'sender_name',
            'receiver_name',
            'email',
            'phone',
            'nic_number',
            'from_num',
            'to_num',
            'amount',
            'description',
        ]);
        $this->mount(); 
    }

    
};
?>


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

<x-mary-form wire:submit.prevent="submit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Sender Details -->
        <div class="col-span-1 md:col-span-2">
            <h2 class="text-lg font-semibold mb-2">Sender Details</h2>
        </div>

        <x-mary-input label="Full Name" wire:model="sender_name" required class="w-full" />
        <x-mary-input label="Email" wire:model="email" type="email" required class="w-full" />
        <x-mary-input label="Phone Number" wire:model="phone" required class="w-full" />
        <x-mary-input label="Account Number" wire:model="from_num" required class="w-full" />
        <x-mary-input label="NIC Number" wire:model="nic_number" required class="w-full" />
        <x-mary-input label="Amount (Rs.)" wire:model="amount" type="number" min="0" required class="w-full" />
        <div class="col-span-1 md:col-span-2">
            <x-mary-textarea label="Description" wire:model="description" rows="2" class="w-full" />
        </div>

        <!-- Recipient Details -->
        <div class="col-span-1 md:col-span-2 mt-4">
            <h2 class="text-lg font-semibold mb-2">Recipient Details</h2>
        </div>

        <x-mary-input label="Full Name" wire:model="receiver_name" required class="w-full" />
        <x-mary-input label="Account Number" wire:model="to_num" required class="w-full" />

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