<?php

use App\Models\Branch;
use Livewire\Volt\Component;

new class extends Component {
    public string $search = '';
    public ?int $branchFilter = null;
    public ?bool $statusFilter = null;

    public function with(): array
    {
        $branches = Branch::all()->map(function ($branch) {
            return ['id' => $branch->id, 'name' => $branch->branch_name];
        })->toArray();

        $statuses = [
            ['id' => true, 'name' => 'Active'],
            ['id' => false, 'name' => 'Inactive'],
        ];

        return [
            'branches' => $branches,
            'statuses' => $statuses,
        ];
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->branchFilter = null;
        $this->statusFilter = null;

        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedBranchFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedStatusFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }
}; ?>

<div>
    <x-mary-card shadow>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            {{-- Search Input --}}
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                placeholder="Search by name, email, or NIC..."
                icon="o-magnifying-glass"
                class="w-full" />

            {{-- Branch Filter --}}
            <x-mary-select
                wire:model.live="branchFilter"
                :options="$branches"
                option-value="id"
                option-label="name"
                placeholder="All Branches"
                icon="o-building-office"
                class="w-full" />

            {{-- Status Filter --}}
            <x-mary-select
                wire:model.live="statusFilter"
                :options="$statuses"
                option-value="id"
                option-label="name"
                placeholder="All Statuses"
                icon="o-flag"
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