<?php

use App\Models\SavingsAccountType;
use App\Models\Branch;
use App\Enums\AccountStatusEnum;
use Livewire\Volt\Component;

new class extends Component {
    public string $search = '';
    public ?int $accountTypeFilter = null;
    public ?int $branchFilter = null;
    public ?string $statusFilter = null;

    public function with(): array
    {
        $accountTypes = SavingsAccountType::all()->map(function ($type) {
            return ['id' => $type->id, 'name' => $type->name];
        })->toArray();

        $branches = Branch::all()->map(function ($branch) {
            return ['id' => $branch->id, 'name' => $branch->branch_name];
        })->toArray();

        $statuses = collect(AccountStatusEnum::cases())->map(function ($status) {
            return ['id' => $status->value, 'name' => $status->name];
        })->toArray();

        return [
            'accountTypes' => $accountTypes,
            'branches' => $branches,
            'statuses' => $statuses,
        ];
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->accountTypeFilter = null;
        $this->branchFilter = null;
        $this->statusFilter = null;

        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'accountTypeFilter' => $this->accountTypeFilter,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'accountTypeFilter' => $this->accountTypeFilter,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedAccountTypeFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'accountTypeFilter' => $this->accountTypeFilter,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedBranchFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'accountTypeFilter' => $this->accountTypeFilter,
            'branchFilter' => $this->branchFilter,
            'statusFilter' => $this->statusFilter,
        ]);
    }

    public function updatedStatusFilter(): void
    {
        $this->dispatch('filters-updated', [
            'search' => $this->search,
            'accountTypeFilter' => $this->accountTypeFilter,
            'branchFilter' => $this->branchFilter,
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
                placeholder="Search by account number or customer name..."
                icon="o-magnifying-glass"
                class="w-full" />

            {{-- Account Type Filter --}}
            <x-mary-select
                wire:model.live="accountTypeFilter"
                :options="$accountTypes"
                option-value="id"
                option-label="name"
                placeholder="All Account Types"
                icon="o-credit-card"
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
