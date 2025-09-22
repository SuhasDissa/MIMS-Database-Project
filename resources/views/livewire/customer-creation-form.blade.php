<?php

use Livewire\Volt\Component;
use App\Models\Branch;

new class extends Component {

    public $branches = [];

    public function mount() {
        // You can fetch branches from the database if needed
        $this->branches = Branch::all()->map(function($branch) {
            return ['id' => $branch->id, 'name' => $branch->branch_name];
        })->toArray();
    }
}; ?>

<x-mary-form wire:submit="save2" >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
        <x-mary-input label="First Name" wire:model="first_name" required class="text-base w-[300px]" />
        <x-mary-input label="Last Name" wire:model="last_name" required class="text-base w-[300px]" />

        <x-mary-input label="Date of Birth" wire:model="dob" type="date" required class="text-base w-[300px]" />
        <x-mary-select label="Gender" wire:model="gender"
            :options="[
                ['id' => 'male', 'name' => 'Male'],
                ['id' => 'female', 'name' => 'Female'],
                ['id' => 'other', 'name' => 'Other'],
            ]"
            required class="text-base" />

        <x-mary-input label="Email" wire:model="email" type="email" required class="text-base w-[300px]" />
        <x-mary-input label="Phone" wire:model="phone" required class="text-base w-[300px]" />

        <x-mary-input label="Address" wire:model="address" required class="text-base w-[300px]" />
        <x-mary-input label="City" wire:model="city" required class="text-base w-[300px]"  />

        <x-mary-input label="Postal Code" wire:model="postal_code" required class="text-base w-[300px]" />
        <x-mary-input label="NIC Number" wire:model="nic_number" required class="text-base w-[300px]" />

        <x-mary-select label="Branch" wire:model="branch_id" :options="$this->branches" 
            required class="text-base w-[300px]" />

    </div>

    <!-- Button -->
    <x-slot:actions>
        <div class="flex justify-center mt-8 w-full">
            <x-mary-button 
                label="Register" 
                class="btn-primary w-full py-6 text-base border-white/50 rounded-lg shadow-md bg-transparent transition-all duration-200 hover:border-white/100 hover:bg-white  hover:text-black font-semibold" 
                type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>
