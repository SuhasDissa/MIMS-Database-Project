<x-layouts.app :title="__('New Transaction')">
    <div class="max-w-7xl mx-auto">
        <x-mary-header title="Transfer Funds" subtitle="Easily transfer money into your savings account securely" separator />

        <x-mary-card class="mt-6">
            <livewire:transaction.saving-transfer-form/>
        </x-mary-card>
    </div>
</x-layouts.app>
