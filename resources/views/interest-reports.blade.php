<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Interest Reports" subtitle="Analyze interest distribution and trends across account types" separator />
        </div>

        {{-- Interest Statistics Component --}}

        <livewire:reports.interest-statistics />

    </div>
</x-layouts.app>
