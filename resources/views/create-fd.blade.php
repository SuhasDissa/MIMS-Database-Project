<x-layouts.app>
    <div class="max-w-7xl mx-auto">
        <x-mary-header title="Create Fixed Deposit" subtitle="Set up a new fixed deposit account" separator />

        <x-mary-card class="mt-6">
            <x-mary-form wire:submit="save2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- FD Number (Read-only) -->
                    <x-mary-input label="FD Number" wire:model="fd_number" readonly />

                    <!-- NIC Number (To verify customer existence) -->
                    <x-mary-input label="NIC Number" wire:model="nic_number" required />

                    <!-- FD Type (Select) -->
                    <x-mary-select label="FD Type" wire:model="fd_type"
                        :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                        required />

                    <!-- Linked Account ID (Savings Account Number) -->
                    <x-mary-input label="Linked Account ID" wire:model="linked_account_id" required />

                    <!-- Interest Frequency in Months (Select) -->
                    <x-mary-select label="Interest Frequency (Months)" wire:model="interest_frequency"
                        :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                        required />

                    <!-- Maturity Number (Optional) -->
                    <x-mary-input label="Maturity Number (Months)" wire:model="maturity_number" />

                    <!-- Interest Payout (Select) -->
                    <x-mary-select label="Interest Payout" wire:model="interest_payout"
                        :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                        required />

                    <!-- Auto Renewal (Select) -->
                    <x-mary-select label="Auto Renewal" wire:model="auto_renewal"
                        :options="['fixed' => 'Fixed', 'recurring' => 'Recurring']"
                        required />
                </div>

                <!-- Button -->
                <x-slot:actions>
                    <x-mary-button
                        label="Create Fixed Deposit"
                        class="btn-primary"
                        type="submit"
                        spinner="save2" />
                </x-slot:actions>
            </x-mary-form>
        </x-mary-card>
    </div>
</x-layouts.app>