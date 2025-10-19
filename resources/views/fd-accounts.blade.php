<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Active FD Accounts" subtitle="View active fixed deposit accounts and their balances" separator />
        </div>

        {{-- FD Accounts Component --}}
        <livewire:reports.fd-accounts />

    </div>
</x-layouts.app>