<x-layouts.app :title="__('New Transaction')">
    <div class="max-w-7xl mx-auto">
        <x-mary-header title="New Transaction" subtitle="Process a account transaction" separator />

        <x-mary-card class="mt-6">
            <livewire:savings-transaction-form />
        </x-mary-card>
    </div>
</x-layouts.app>
