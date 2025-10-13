<?php

use Livewire\Volt\Component;
use App\Models\SavingsAccountType;
use App\Models\CustomerStatusType;

new class extends Component {
    public $name = '';
    public $customer_type = '';
    public $min_balance = '';
    public $interest_rate = '';
    public $description = '';
    public $types = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'customer_type' => 'required|integer|exists:customer_status_types,id',
        'min_balance' => 'required|numeric|min:0',
        'interest_rate' => 'required|numeric|min:0',
        'description' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $this->types = CustomerStatusType::all()
            ->map(fn($type) => ['id' => $type->id, 'name' => $type->status_name])
            ->toArray();
    }

    public function submit()
    {
        $this->validate();

        SavingsAccountType::create([
            'name' => $this->name,
            'customer_status_id' => $this->customer_type,
            'min_balance' => $this->min_balance,
            'interest_rate' => $this->interest_rate,
            'description' => $this->description,
            'is_active' => 1, // default active
        ]);

        $this->reset();
        session()->flash('success', 'Savings Account Type created!');
    }
};
?>

<!-- Form -->
<x-mary-form wire:submit="saveSavingsAccount">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
        <!-- Account Name -->
        <x-mary-input label="Account Name" wire:model="account_name" required 
            class="text-base w-[300px] focus:ring-0 focus:outline-none" />

        <!-- Customer Type (Dropdown) -->
        <x-mary-select label="Customer Type" wire:model="customer_type"
            :options="$types" option-label="name" option-value="id"
             class="text-base w-[300px] focus:ring-0 focus:outline-none" />

        <!-- Minimum Balance -->
        <x-mary-input label="Minimum Balance (Rs.)" wire:model="minimum_balance" type="number" min="0" required
            class="text-base w-[300px] focus:ring-0 focus:outline-none" />

        <!-- Interest Rate -->
        <x-mary-input label="Interest Rate (%)" wire:model="interest_rate" type="number" step="0.01" min="0" required
            class="text-base w-[300px] focus:ring-0 focus:outline-none" />

        <!-- Description (Text Area) -->
        <div class="col-span-1 md:col-span-2">
            <x-mary-textarea label="Description" wire:model="description" rows="4"
                class="text-base w-full focus:ring-0 focus:outline-none" />
        </div>
    </div>

    <!-- Button -->
    <x-slot:actions>
        <div class="flex justify-center mt-8 w-full">
            <x-mary-button 
                label="Create Savings Account Type"  
                class="btn-primary w-full py-6 text-base border-white/50 rounded-lg shadow-md bg-transparent transition-all duration-200 hover:border-white/100 hover:bg-white hover:text-black font-semibold" 
                type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>
