<x-layouts.app>
    <div class="max-w-7xl mx-auto">
        <x-mary-header title="Add Fixed Deposit Type" subtitle="Configure a new fixed deposit type" separator />

        <x-mary-card class="mt-6">
            <x-mary-form wire:submit="saveFdType">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- FD Name -->
                    <x-mary-input label="FD Name" wire:model="fd_name" required />

                    <!-- Minimum Deposit -->
                    <x-mary-input label="Minimum Deposit (Rs.)" wire:model="min_deposit" type="number" min="0" required />

                    <!-- Interest Rate -->
                    <x-mary-input label="Interest Rate (%)" wire:model="interest_rate" type="number" step="0.01" min="0" required />

                    <!-- Tenure (Months) -->
                    <x-mary-input label="Tenure (Months)" wire:model="tenure_months" type="number" min="1" required />

                    <!-- Description (Text Area) -->
                    <div class="col-span-1 md:col-span-2">
                        <x-mary-textarea label="Description" wire:model="description" rows="4" />
                    </div>
                </div>

                <!-- Button -->
                <x-slot:actions>
                    <x-mary-button
                        label="Create FD Type"
                        class="btn-primary"
                        type="submit"
                        spinner="saveFdType" />
                </x-slot:actions>
            </x-mary-form>
        </x-mary-card>
    </div>
</x-layouts.app>