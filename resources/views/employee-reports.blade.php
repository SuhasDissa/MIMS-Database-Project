<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Agent Customer Report" subtitle="Comprehensive overview of customers managed by each agent" separator />
        </div>

        {{-- Interest Statistics Component --}}

        <livewire:reports.employee-reports/>

    </div>
</x-layouts.app>
