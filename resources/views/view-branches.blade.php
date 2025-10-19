@php
    use App\Models\Branch;
    use App\Models\Customer;
    use App\Models\Employee;
    use App\Models\SavingsAccount;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Branches" subtitle="View and manage all branches" separator />
        </div>

        {{-- Filters Component --}}
        <livewire:branches.filters />

        {{-- Branches Table Component --}}
        <livewire:branches.table />

        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Branches"
                :value="Branch::count()"
                icon="o-building-office"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Customers"
                :value="Customer::count()"
                icon="o-users"
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