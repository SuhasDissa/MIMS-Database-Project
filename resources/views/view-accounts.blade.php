@php
    use App\Models\SavingsAccount;
    use App\Models\Customer;

    // Filter accounts based on employee role
    $accountQuery = SavingsAccount::query();

    if (auth()->user()->canOnlyManageAssignedCustomers()) {
        // Agent: only show accounts belonging to their assigned customers
        $assignedCustomerIds = Customer::where('employee_id', auth()->id())->pluck('id');

        $accountQuery->whereHas('customers', function($query) use ($assignedCustomerIds) {
            $query->whereIn('customer_id', $assignedCustomerIds);
        });
    }

    $totalAccounts = $accountQuery->count();
    $activeAccounts = (clone $accountQuery)->where('status', 'ACTIVE')->count();
    $totalBalance = (clone $accountQuery)->sum('balance');

    // Set appropriate subtitle based on role
    $subtitle = auth()->user()->canOnlyManageAssignedCustomers()
        ? 'View and manage accounts for your assigned customers'
        : 'View and manage all savings accounts';
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Savings Accounts" :subtitle="$subtitle" separator>
                @if(auth()->user()->canOnlyManageAssignedCustomers())
                    <x-slot:actions>
                        <div class="badge badge-info badge-soft gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-4 h-4 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Showing accounts for your assigned customers
                        </div>
                    </x-slot:actions>
                @endif
            </x-mary-header>
        </div>

        {{-- Filters Component --}}
        <livewire:accounts.filters />


        {{-- Accounts Table Component --}}
        <livewire:accounts.table />


        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Accounts"
                :value="$totalAccounts"
                icon="o-credit-card"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Active Accounts"
                :value="$activeAccounts"
                icon="o-check-circle"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Balance"
                value="Rs. {{ number_format($totalBalance, 2) }}"
                icon="o-banknotes"
                color="text-accent"
                class="bg-base-100 shadow-md" />
        </div>
    </div>
</x-layouts.app>
