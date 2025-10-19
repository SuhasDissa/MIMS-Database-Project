<x-layouts.app :title="__('New Transaction')">
    <div class="max-w-7xl mx-auto">
        <x-mary-header title="Withdraw Funds" subtitle="Easily withdraw money into your savings account securely" separator />

        <x-mary-card class="mt-6">
            <livewire:transaction.saving-withdraw-form/>
        </x-mary-card>
    </div>
</x-layouts.app>
