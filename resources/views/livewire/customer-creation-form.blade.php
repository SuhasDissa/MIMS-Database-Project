<?php

use Livewire\Volt\Component;
use App\Models\Branch;
use App\Enums\GenderEnum;
use App\Models\Customer;
use App\Models\CustomerStatusType;

new class extends Component {
    public $branches = [];
    public $statuses = [];

    public $first_name = '';
    public $last_name = '';
    public $date_of_birth = '';
    public $gender = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $id_type = 'NIC';
    public $id_number = '';
    public $status_id = '';
    public $branch_id = '';

    public function submit()
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'id_type' => $this->id_type,
            'id_number' => $this->id_number,
            'status_id' => $this->status_id,
            'branch_id' => $this->branch_id,
        ];

        Customer::create($data);
        $this->reset();
    }

    public function mount()
    {
        $this->branches = Branch::all()
            ->map(function ($branch) {
                return ['id' => $branch->id, 'name' => $branch->branch_name];
            })
            ->toArray();

        $this->statuses = CustomerStatusType::all()
            ->map(function ($status) {
                return ['id' => $status->id, 'name' => $status->status_name];
            })
            ->toArray();
    }
}; ?>

<x-mary-form wire:submit="submit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
        <x-mary-input label="First Name" wire:model="first_name" required class="text-base w-[300px]" />
        <x-mary-input label="Last Name" wire:model="last_name" required class="text-base w-[300px]" />

        <x-mary-input label="Date of Birth" wire:model="date_of_birth" type="date" required
            class="text-base w-[300px]" />
        <x-mary-select label="Gender" wire:model="gender" :options="GenderEnum::asSelectArray()" required class="text-base" />

        <x-mary-input label="Email" wire:model="email" type="email" required class="text-base w-[300px]" />
        <x-mary-input label="Phone" wire:model="phone" required class="text-base w-[300px]" />

        <x-mary-input label="Address" wire:model="address" required class="text-base w-[300px]" />
        <x-mary-input label="City" wire:model="city" required class="text-base w-[300px]" />
        <x-mary-input label="State" wire:model="state" required class="text-base w-[300px]" />

        <x-mary-input label="Postal Code" wire:model="postal_code" required class="text-base w-[300px]" />

        <x-mary-input label="ID Type" wire:model="id_type" required class="text-base w-[300px]" />
        <x-mary-input label="NIC Number" wire:model="id_number" required class="text-base w-[300px]" />

        <x-mary-select label="Status" wire:model="status_id" :options="$this->statuses" required class="text-base w-[300px]" />

        <x-mary-select label="Branch" wire:model="branch_id" :options="$this->branches" required class="text-base w-[300px]" />

    </div>

    <!-- Button -->
    <x-slot:actions>
        <div class="flex justify-center mt-8 w-full">
            <x-mary-button label="Register"
                class="btn-primary w-full py-6 text-primary dark:text-base border-white/50 rounded-lg shadow-md bg-transparent transition-all duration-200 hover:border-white/100 hover:bg-white  hover:text-black font-semibold"
                type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>
