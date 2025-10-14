<?php

use Livewire\Volt\Component;
use App\Models\Employee;
use App\Models\Branch;

new class extends Component {
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $phone = '';
    public $position = '';
    public $nic_num = '';
    public $branch_id = '';
    public $is_active = true;

    public $branches = [];

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:employee,email',
        'phone' => 'required|string|max:15',
        'position' => 'required|string|max:100',
        'nic_num' => 'required|string|max:20|unique:employee,nic_num',
        'branch_id' => 'required|integer|exists:branch,id',
        'is_active' => 'required|boolean',
    ];

    public function mount() {
        // You can fetch branches from the database if needed
        $this->branches = Branch::all()->map(function($branch) {
            return ['id' => $branch->id, 'name' => $branch->branch_name];
        })->toArray();
    }

    public function submit(){
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'nic_num' => $this->nic_num,
            'branch_id' => $this->branch_id,
            'is_active' => $this->is_active,
        ];

        Employee::create($data);
        $this->reset();
    }
}; ?>

<x-mary-form wire:submit="submit" >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
        <x-mary-input label="First Name" wire:model="first_name" required class="text-base w-[300px]" />
        <x-mary-input label="Last Name" wire:model="last_name" required class="text-base w-[300px]" />
        <x-mary-input label="Email" wire:model="email" type="email" required class="text-base w-[300px]" />
        <x-mary-input label="Phone" wire:model="phone" required class="text-base w-[300px]" />
        <x-mary-input label="Position" wire:model="position" required class="text-base w-[300px]" />
        <x-mary-input label="NIC Number" wire:model="nic_num" required class="text-base w-[300px]" />
        <x-mary-select label="Branch" wire:model="branch_id" required class="text-base w-[300px]" :options="$this->branches" />
        <x-mary-select label="Active" wire:model="is_active" required class="text-base w-[300px]" :options="[
            ['id' => true, 'name' => 'Active'],
            ['id' => false, 'name' => 'Inactive'],
        ]" />
    </div>

    <!-- Button -->
    <x-slot:actions>
        <div class="flex justify-end mt-8 w-full">
            <x-mary-button 
                label="Create Employee" 
                class="btn-primary" 
                type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>
