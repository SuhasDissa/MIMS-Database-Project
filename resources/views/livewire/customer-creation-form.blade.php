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

//     protected $rules = [
//         'first_name' => 'required|string|max:255',
//         'last_name' => 'required|string|max:255',
//         'date_of_birth' => 'required|date',
//         'gender' => 'required|string',
//         'email' => 'required|email|max:255|unique:customers,email',
//         'phone' => 'required|string|max:20',
//         'address' => 'required|string|max:255',
//         'city' => 'required|string|max:255',
//         'state' => 'required|string|max:255',
//         'postal_code' => 'required|string|max:10',
//         'id_type' => 'required|string|max:20',
//         'id_number' => 'required|string|max:50|unique:customers,id_number',
//         'status_id' => 'required|exists:customer_status_types,id',
//         'branch_id' => 'required|exists:branches,id',
// ];

    public function submit()
    {
        // $this->validate();

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
            // Attach the currently authenticated employee creating this customer
            'employee_id' => auth()->id(),
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
        <x-mary-select label="Gender" wire:model="gender" placeholder="Select a gender"
        :options="[
        ['id' => 'M', 'name' => 'Male'],
        ['id' => 'F', 'name' => 'Female'],
        ['id' => 'O', 'name' => 'Other']
    ]" 
        required class="text-base" />

        <x-mary-input label="Email" wire:model="email" type="email" required class="text-base w-[300px]" />
        <x-mary-input label="Phone" wire:model="phone" required class="text-base w-[300px]" />

        <x-mary-input label="Address" wire:model="address" required class="text-base w-[300px]" />
        <x-mary-input label="City" wire:model="city" required class="text-base w-[300px]" />
        <x-mary-input label="State" wire:model="state" required class="text-base w-[300px]" />

        <x-mary-input label="Postal Code" wire:model="postal_code" required class="text-base w-[300px]" />

        <x-mary-input label="ID Type" wire:model="id_type" required class="text-base w-[300px]" />
        <x-mary-input label="NIC Number" wire:model="id_number" required class="text-base w-[300px]" />

        <x-mary-select label="Status" wire:model="status_id" :options="$this->statuses" placeholder="Select a status" class="text-base w-[300px]" />

        <x-mary-select label="Branch" wire:model="branch_id" :options="$this->branches" placeholder="Select a branch" class="text-base w-[300px]" />

    </div>

    <!-- Button -->
    <x-slot:actions>
        <div class="flex justify-end mt-8 w-[200px]">
            <x-mary-button label="Register"
                class="btn-primary w-full"
                type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>
