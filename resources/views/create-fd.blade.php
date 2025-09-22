<x-layouts.auth.clean>
    <div class=" flex h-[100dvh] w-[100%] items-center justify-center m-0 p-0">
                <div class="w-[50%] h-full flex items-start justify-center">
                    <img src="{{ asset('bank2.jpg') }}" alt="Customer Registration" class="w-full h-full">
                </div>
                <div class="p-6 w-[50%] flex items-center justify-center flex-col">
                    <!-- Title -->
                    <h2 class="text-4xl font-bold text-white-800 mb-10 text-center">
                        Fixed Deposit
                    </h2>

                    <!-- Form -->
                    <x-mary-form wire:submit="save2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">
                            <!-- FD Number (Read-only) -->
                            <x-mary-input label="FD Number" wire:model="fd_number" readonly class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- NIC Number (To verify customer existence) -->
                            <x-mary-input label="NIC Number" wire:model="nic_number" required class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- FD Type (Select) -->
                            <x-mary-select label="FD Type" wire:model="fd_type" 
                                :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                                required class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Linked Account ID (Savings Account Number) -->
                            <x-mary-input label="Linked Account ID" wire:model="linked_account_id" required class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Interest Frequency in Months (Select) -->
                            <x-mary-select label="Interest Frequency (Months)" wire:model="interest_frequency" 
                                :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                                required class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Maturity Number (Optional) -->
                            <x-mary-input label="Maturity Number (Months)" wire:model="maturity_number" class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Interest Payout (Select) -->
                            <x-mary-select label="Interest Payout" wire:model="interest_payout" 
                                :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                                required class="text-base w-[300px] focus:ring-0 focus:outline-none" />

                            <!-- Auto Renewal (Select) -->
                            <x-mary-select label="Auto Renewal" wire:model="auto_renewal" 
                                :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                                required class="text-base w-[300px] focus:ring-0 focus:outline-none" />
                        </div>

                        <!-- Button -->
                        <x-slot:actions>
                            <div class="flex justify-center mt-8 w-full">
                                <x-mary-button 
                                    label="Create Fixed Deposit"  
                                    class="btn-primary w-full py-6 text-base border-white/50 rounded-lg shadow-md bg-transparent transition-all duration-200 hover:border-white/100 hover:bg-white hover:text-black font-semibold" 
                                    type="submit" />
                            </div>
                        </x-slot:actions>
                    </x-mary-form>
                </div>
            </div>
</x-layouts.auth.clean>