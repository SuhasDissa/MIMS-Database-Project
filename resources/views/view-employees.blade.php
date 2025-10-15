@php
    use App\Models\Employee;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Employees" subtitle="View and manage all employees" separator />
        </div>

        {{-- Filters Component --}}
        <livewire:employees.filters />


        {{-- Employees Table Component --}}
        <livewire:employees.table />


        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Employees"
                :value="Employee::count()"
                icon="o-users"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Active Employees"
                :value="Employee::where('is_active', true)->count()"
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Inactive Employees"
                :value="Employee::where('is_active', false)->count()"
                icon="o-x-circle"
                color="text-error"
                class="bg-base-100 shadow-md" />
        </div>
    </div>
</x-layouts.app>