@php
    use App\Models\SavingsAccount;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Savings Accounts" subtitle="View and manage all savings accounts" separator />
        </div>

        {{-- Filters Component --}}
        <livewire:accounts.filters />


        {{-- Accounts Table Component --}}
        <livewire:accounts.table />
 

        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Accounts"
                :value="SavingsAccount::count()"
                icon="o-credit-card"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Active Accounts"
                :value="SavingsAccount::where('status', 'ACTIVE')->count()"
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Balance"
                value="Rs. {{ number_format(SavingsAccount::sum('balance'), 2) }}"
                icon="o-banknotes"
                color="text-accent"
                class="bg-base-100 shadow-md" />
        </div>
    </div>
</x-layouts.app>
