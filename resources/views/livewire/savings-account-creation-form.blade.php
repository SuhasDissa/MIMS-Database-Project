<?php

use Livewire\Volt\Component;
use App\Models\SavingsAccount;
use App\Models\SavingsAccountType;
use App\Models\Branch;
use App\Models\Customer;
use App\Enums\AccountStatusEnum;

new class extends Component {
    public $accountTypes = [];
    public $branches = [];
    public $customers = [];

    public $account_number = '';
    public $account_type_id = '';
    public $branch_id = '';
    public $balance = 0;
    public $status = '';
    public $opened_date = '';
    public $customer_ids = [];

    protected $rules = [
        'account_number' => 'required|string|max:256|unique:savings_account,account_number',
        'account_type_id' => 'required|exists:savings_account_type,id',
        'branch_id' => 'required|exists:branch,id',
        'balance' => 'required|numeric|min:0',
        'status' => 'required|in:ACTIVE,INACTIVE',
        'opened_date' => 'required|date',
        'customer_ids' => 'required|array|min:1',
        'customer_ids.*' => 'exists:customers,id',
    ];

    public function mount()
    {
        $this->accountTypes = SavingsAccountType::where('is_active', true)
            ->get()
            ->map(function ($type) {
                return ['id' => $type->id, 'name' => $type->name];
            })
            ->toArray();

        $this->branches = Branch::all()
            ->map(function ($branch) {
                return ['id' => $branch->id, 'name' => $branch->branch_name];
            })
            ->toArray();

        $this->customers = Customer::all()
            ->map(function ($customer) {
                return ['id' => $customer->id, 'name' => $customer->first_name . ' ' . $customer->last_name . ' (' . $customer->id_number . ')'];
            })
            ->toArray();

        $this->status = AccountStatusEnum::ACTIVE->value;
        $this->opened_date = now()->format('Y-m-d');
    }

    public function submit()
    {
        $this->validate();

        $account = SavingsAccount::create([
            'account_number' => $this->account_number,
            'account_type_id' => $this->account_type_id,
            'branch_id' => $this->branch_id,
            'balance' => $this->balance,
            'status' => $this->status,
            'opened_date' => $this->opened_date,
        ]);

        $account->customers()->attach($this->customer_ids);

        $this->dispatch('toast', title: 'Savings account created successfully.');

        $this->reset();
    }
}; ?>

<x-mary-form wire:submit="submit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Account Number -->
        <x-mary-input label="Account Number" wire:model="account_number" required />

        <!-- Account Type -->
        <x-mary-select label="Account Type" wire:model="account_type_id"
            :options="$this->accountTypes"
            required />

        <!-- Branch -->
        <x-mary-select label="Branch" wire:model="branch_id"
            :options="$this->branches"
            required />

        <!-- Initial Balance -->
        <x-mary-input label="Initial Balance" wire:model="balance" type="number" step="0.01" min="0" required />


        <!-- Opened Date -->
        <x-mary-input label="Opened Date" wire:model="opened_date" type="date" required />

        <!-- Customers (Multi-select) -->
        <div class="md:col-span-2">
            <x-mary-select label="Customers" wire:model="customer_ids"
                :options="$this->customers"
                multiple
                searchable
                required />
        </div>
    </div>

    <!-- Button -->
    <x-slot:actions>
        <x-mary-button
            label="Create Savings Account"
            class="btn-primary"
            type="submit" />
    </x-slot:actions>
</x-mary-form>