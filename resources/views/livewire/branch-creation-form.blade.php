<?php

use Livewire\Volt\Component;
use App\Models\Branch;

new class extends Component {
    public $branch_name;
    public $branch_code;
    public $address;
    public $city;
    public $postal_code;
    public $email;
    public $phone;

    protected $rules = [
        'branch_name' => 'required|string|max:255',
        'branch_code' => 'required|string|max:50|unique:branch,branch_code',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'postal_code' => 'required|string|max:20',
        'email' => 'required|email',
        'phone' => 'required|string|max:15',
    ];

    public function submit()
    {
        $this->validate();

        Branch::create([
            'branch_name' => $this->branch_name,
            'branch_code' => $this->branch_code,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => true,
        ]);

        $this->reset();
    }

    
}; ?>

 <x-mary-form wire:submit="submit" >
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
        <x-mary-input label="Branch Name" wire:model="branch_name" required class="text-base w-[300px]" />
        <x-mary-input label="Branch Code" wire:model="branch_code" required class="text-base w-[300px]" />

        <x-mary-input label="Branch Address" wire:model="address" required class="text-base w-[300px]" />
        <x-mary-input label="City" wire:model="city" required class="text-base w-[300px]" />
        <x-mary-input label="Postal Code" wire:model="postal_code" required class="text-base w-[300px]" />
        <x-mary-input label="Email" wire:model="email" type="email" required class="text-base w-[300px]" />
        <x-mary-input label="Phone" wire:model="phone" required class="text-base w-[300px]" />
        
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