<x-layouts.app :title="__('New Transaction')">
    <div class="max-w-7xl mx-auto">
        <x-mary-header title="Deposit Funds" subtitle="Easily deposit money into your savings account securely" separator />

        <x-mary-card class="mt-6">
            <livewire:transaction.saving-deposit-form/>
        </x-mary-card>
    </div>
</x-layouts.app>
