@php
    use App\Models\Customer;
    use App\Models\SavingsAccount;
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
                title="Senior Customers"
                :value="Customer::where('status_id', 2)->count()" {{-- Assuming 2 is senior --}}
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Junior Customers"
                :value="Customer::where('status_id', 1)->count()" {{-- Assuming 1 is junior --}}
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Savings Accounts"
                :value="SavingsAccount::count()"
                icon="o-credit-card"
                color="text-accent"
                class="bg-base-100 shadow-md" />
        </div>
    </div>
</x-layouts.app>