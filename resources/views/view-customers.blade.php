@php
    use App\Models\Customer;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Customers" subtitle="View and manage all customers" separator />
        </div>

        {{-- Filters Component --}}
        <livewire:customers.filters />


        {{-- Customers Table Component --}}
        <livewire:customers.table />


        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Customers"
                :value="Customer::count()"
                icon="o-users"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Active Customers"
                :value="Customer::where('status_id', 1)->count()" {{-- Assuming 1 is active --}}
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Savings Accounts"
                :value="Customer::with('savingsAccounts')->get()->sum(fn($c) => $c->savingsAccounts->count())"
                icon="o-credit-card"
                color="text-accent"
                class="bg-base-100 shadow-md" />
        </div>
    </div>
</x-layouts.app>