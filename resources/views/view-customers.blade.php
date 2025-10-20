@php
    use App\Models\Customer;
    use App\Models\SavingsAccount;

    // Filter customers based on employee role
    $customerQuery = Customer::query();

    if (auth()->user()->canOnlyManageAssignedCustomers()) {
        // Agent: only show assigned customers
        $customerQuery->where('employee_id', auth()->id());
    }

    $totalCustomers = $customerQuery->count();
    $seniorCustomers = (clone $customerQuery)->where('status_id', 2)->count();
    $juniorCustomers = (clone $customerQuery)->where('status_id', 1)->count();

    // Savings accounts for the filtered customers
    $customerIds = $customerQuery->pluck('id');
    $totalSavingsAccounts = SavingsAccount::whereHas('customers', function($query) use ($customerIds) {
        $query->whereIn('customer_id', $customerIds);
    })->count();

    // Set appropriate subtitle based on role
    $subtitle = auth()->user()->canOnlyManageAssignedCustomers()
        ? 'View and manage your assigned customers'
        : 'View and manage all customers';
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Customers" :subtitle="$subtitle" separator>
                @if(auth()->user()->canOnlyManageAssignedCustomers())
                    <x-slot:actions>
                        <div class="badge badge-info badge-soft gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Showing only your assigned customers
                        </div>
                    </x-slot:actions>
                @endif
            </x-mary-header>
        </div>

        {{-- Filters Component --}}
        <livewire:customers.filters />


        {{-- Customers Table Component --}}
        <livewire:customers.table />


        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Customers"
                :value="$totalCustomers"
                icon="o-users"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Senior Customers"
                :value="$seniorCustomers"
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Junior Customers"
                :value="$juniorCustomers"
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Savings Accounts"
                :value="$totalSavingsAccounts"
                icon="o-credit-card"
                color="text-accent"
                class="bg-base-100 shadow-md" />
        </div>
    </div>
</x-layouts.app>