<x-layouts.auth.clean>
    <div class=" flex h-[100dvh] w-[100%] items-center justify-center m-0 p-0">
                <div class="w-[50%] h-full flex items-start justify-center">
                    <img src="{{ asset('bank2.jpg') }}" alt="Customer Registration" class="w-full h-full">
                </div>
                <div class="p-6 w-[50%] flex items-center justify-center flex-col">
                    <!-- Title -->
                    <h2 class="text-4xl font-bold text-white-800 mb-10 text-center">
                        Add Savings Account Type
                    </h2>

                    <!-- Form -->
                    <x-mary-form wire:submit="saveSavingsAccount">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
                            <!-- Account Name -->
                            <x-mary-input label="Account Name" wire:model="account_name" required 
                                class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Customer Type (Dropdown) -->
                            <x-mary-select label="Customer Type" wire:model="customer_type"
                                :options="['individual' => 'Individual', 'business' => 'Business', 'student' => 'Student', 'senior' => 'Senior Citizen']"
                                required class="text-base w-[300px] focus:ring-0 focus:outline-none" />

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

                </div>
            </div>
</x-layouts.auth.clean>