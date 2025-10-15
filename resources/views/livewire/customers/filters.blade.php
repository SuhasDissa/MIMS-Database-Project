<?php

use App\Models\Branch;
use App\Enums\GenderEnum;
use App\Models\CustomerStatusType;
use Livewire\Volt\Component;

new class extends Component {
    public string $search = '';
    public ?int $branchFilter = null;
    public ?string $genderFilter = null;
    public ?int $statusFilter = null;

    public function with(): array
    {
        $branches = Branch::all()->map(function ($branch) {
            return ['id' => $branch->id, 'name' => $branch->branch_name];
        })->toArray();

        $genders = collect(GenderEnum::cases())->map(function ($gender) {
            return ['id' => $gender->value, 'name' => $gender->name];
        })->toArray();

        $statuses = CustomerStatusType::all()->map(function ($status) {
            return ['id' => $status->id, 'name' => $status->name];
        })->toArray();

        return [
            'branches' => $branches,
            'genders' => $genders,
            'statuses' => $statuses,
        ];
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->branchFilter = null;
        $this->genderFilter = null;
        $this->statusFilter = null;

        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'genderFilter' => $this->genderFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'genderFilter' => $this->genderFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedBranchFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'genderFilter' => $this->genderFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedGenderFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'genderFilter' => $this->genderFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedStatusFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'branchFilter' => $this->branchFilter,
            'genderFilter' => $this->genderFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }
}; ?>

<div>
    <x-mary-card shadow>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Search Input --}}
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                placeholder="Search by name, email, or phone..."
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

            {{-- Gender Filter --}}
            <x-mary-select
                wire:model.live="genderFilter"
                :options="$genders"
                option-value="id"
                option-label="name"
                placeholder="All Genders"
                icon="o-user"
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