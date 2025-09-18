<x-layouts.auth.clean>
    <div class=" flex h-[100dvh] w-[100%] items-center justify-center m-0 p-0">
                <div class="w-[50%] h-full flex items-start justify-center">
                    <img src="{{ asset('bank.jpg') }}" alt="Customer Registration" class="w-full h-full">
                </div>
                <div class="p-6 w-[50%] flex items-center justify-center flex-col">
                    <!-- Title -->
                    <h2 class="text-4xl font-bold text-white-800 mb-10 text-center">
                        Customer Registration
                    </h2>

                    <!-- Form -->
                    <x-mary-form wire:submit="save2" >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
                            <x-mary-input label="First Name" wire:model="first_name" required class="text-base w-[300px]" />
                            <x-mary-input label="Last Name" wire:model="last_name" required class="text-base w-[300px]" />

                            <x-mary-input label="Date of Birth" wire:model="dob" type="date" required class="text-base w-[300px]" />
                            <x-mary-select label="Gender" wire:model="gender"
                                :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
                                required class="text-base" />

                            <x-mary-input label="Email" wire:model="email" type="email" required class="text-base w-[300px]" />
                            <x-mary-input label="Phone" wire:model="phone" required class="text-base w-[300px]" />

                            <x-mary-input label="Address" wire:model="address" required class="text-base w-[300px]" />
                            <x-mary-input label="City" wire:model="city" required class="text-base w-[300px]"  />

                            <x-mary-input label="Postal Code" wire:model="postal_code" required class="text-base w-[300px]" />
                            <x-mary-input label="NIC Number" wire:model="nic_number" required class="text-base w-[300px]" />

                            <x-mary-select label="Branch" wire:model="state" required class="text-base w-[300px]" />

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
                </div>
            </div>
</x-layouts.auth.clean>