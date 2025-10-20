<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $search = '';

    public function with(): array
    {
        return [];
    }

    public function clearFilters(): void
    {
        $this->search = '';

        $this->dispatch('filters-updated', [
            'search' => $this->search,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
        ]);
    }
}; ?>

<div>
    <x-mary-card shadow>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Search Input --}}
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                placeholder="Search by branch code or name..."
                icon="o-magnifying-glass"
                class="w-full" />
        </div>

        {{-- Clear Filters Button --}}
        <div class="flex justify-end">
            <x-mary-button
                wire:click="clearFilters"
                label="Clear Filters"
                icon="o-x-mark"
                class="btn-ghost btn-sm" />
        </div>
    </x-mary-card>
</div>