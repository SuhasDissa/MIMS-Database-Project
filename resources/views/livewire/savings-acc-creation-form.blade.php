<?php

use Livewire\Volt\Component;

new class extends Component
{
    //
}; ?>

<x-mary-form wire:submit="save">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Account Number (Read-only) -->
        <x-mary-input
            label="Account Number"
            wire:model="account_number"
            readonly
        />

        <!-- Customer NIC -->
        <x-mary-input
            label="Customer NIC"
            wire:model="nic_number"
            required
        />

        <!-- Customer Name (Auto-filled after NIC validation) -->
        <x-mary-input
            label="Customer Name"
            wire:model="customer_name"
            readonly
        />

        <!-- Account Type (Dropdown) -->
        <x-mary-select
            label="Account Type"
            wire:model="account_type"
            :options="[
                'regular' => 'Regular Savings',
                'student' => 'Student Savings',
                'senior' => 'Senior Citizen Savings',
                'minor' => 'Minor Savings'
            ]"
            required
        />

        <!-- Initial Deposit -->
        <x-mary-input
            label="Initial Deposit (LKR)"
            wire:model="initial_deposit"
            type="number"
            required
        />



    </div>

    <!-- Actions -->
    <x-slot:actions>
        <x-mary-button
            label="Create Savings Account"
            class="btn-primary"
            type="submit"
            {{-- spinner="save" --}}
        />
    </x-slot:actions>
</x-mary-form>