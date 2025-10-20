<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Customer Report Overview" subtitle="Detailed insights of customers managed by each employee" separator />
        </div>

        {{-- Interest Statistics Component --}}

    <livewire:reports.employee-wise-customers :employee="$employee" />

    </div>
</x-layouts.app>
