<x-layouts.auth.clean>
    <div class=" flex h-[100dvh] w-[100%] items-center justify-center m-0 p-0">
                <div class="w-[50%] h-full flex items-start justify-center">
                    <img src="{{ asset('bank2.jpg') }}" alt="Customer Registration" class="w-full h-full">
                </div>
                <div class="p-6 w-[50%] flex items-center justify-center flex-col">
                    <!-- Title -->
                    <h2 class="text-4xl font-bold text-white-800 mb-10 text-center">
                        Add Fixed Deposit Type
                    </h2>

                    <!-- Form -->
                    <x-mary-form wire:submit="saveFdType">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
                            <!-- FD Name -->
                            <x-mary-input label="FD Name" wire:model="fd_name" required 
                                class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Minimum Deposit -->
                            <x-mary-input label="Minimum Deposit (Rs.)" wire:model="min_deposit" type="number" min="0" required
                                class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Interest Rate -->
                            <x-mary-input label="Interest Rate (%)" wire:model="interest_rate" type="number" step="0.01" min="0" required
                                class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Tenure (Months) -->
                            <x-mary-input label="Tenure (Months)" wire:model="tenure_months" type="number" min="1" required
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
                                    label="Create FD Type"  
                                    class="btn-primary w-full py-6 text-base border-white/50 rounded-lg shadow-md bg-transparent transition-all duration-200 hover:border-white/100 hover:bg-white hover:text-black font-semibold" 
                                    type="submit" />
                            </div>
                        </x-slot:actions>
                    </x-mary-form>

                  

                </div>
            </div>
</x-layouts.auth.clean>