@php
    use App\Models\SavingsTransaction;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Transactions" subtitle="View and manage all transactions" separator />
        </div>

        {{-- Filters Component --}}
        <livewire:transaction.filters/>

        {{-- Transactions Table Component --}}
        <livewire:transaction.table />


        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-3">
            <x-mary-stat
                title="Total Transactions"
                :value="SavingsTransaction::count()"
                icon="o-credit-card"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Amount"
                :value="number_format(SavingsTransaction::sum('amount'), 2)"
                icon="o-currency-dollar"
                color="text-success"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Completed Transactions"
                :value="SavingsTransaction::where('status', \App\Enums\TransactionStatusEnum::COMPLETED)->count()"
                icon="o-check-circle"
                color="text-info"
                class="bg-base-100 shadow-md" />
        </div>
    </div>
</x-layouts.app>
