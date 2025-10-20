<?php

use Livewire\Volt\Component;


new class extends Component {
    public string $search = '';
    public ?string $Type = null;
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public ?float $minAmount = null;
    public ?float $maxAmount = null;



    public function with(): array
    {
        $types = collect(\App\Enums\TransactionTypeEnum::cases())->map(function ($type) {
            return ['id' => $type->value, 'name' => $type->name];
        })->toArray();

        return [
            'types' => $types,
        ];
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->Type = null;
        $this->fromDate = null;
        $this->toDate = null;
        $this->minAmount = null;
        $this->maxAmount = null;

        $this->dispatch('filters-updated', [
            'search' => $this->search,
            //'Type' => $this->Type,
            'Type' => $this->Type !== null ? (string) $this->Type : null,

            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'minAmount' => $this->minAmount,
            'maxAmount' => $this->maxAmount,
        ]);
    }

    public function updated(): void
    {
        $this->dispatch('filters-updated', [
            'search'    => $this->search,
            'Type'      => $this->Type,
            'fromDate'  => $this->fromDate,
            'toDate'    => $this->toDate,
            'minAmount' => $this->minAmount,
            'maxAmount' => $this->maxAmount,
        ]);
    }

    
}; ?>


<div>
    <x-mary-card shadow>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
        {{-- Search Input --}}
        <x-mary-input
            wire:model.live.debounce.300ms="search"
            placeholder="Search by name, email, or NIC..."
            icon="o-magnifying-glass"
            class="w-full" />

        {{-- Transaction Type --}}
        <x-mary-select
            wire:model.live="Type"
            :options="$types"
            option-value="id"
            option-label="name"
            placeholder="All Types"
            icon="o-credit-card"
            class="w-full" />

        {{-- Amount Min --}}
        <x-mary-input
            wire:model.live="minAmount"
            type="number"
            step="0.01"
            placeholder="Min Amount"
            icon="o-currency-dollar"
            class="w-full" />

        {{-- Amount Max --}}
        <x-mary-input
            wire:model.live="maxAmount"
            type="number"
            step="0.01"
            placeholder="Max Amount"
            icon="o-currency-dollar"
            class="w-full" />

        {{-- From Date --}}
        <div class="flex flex-col">
            <label class="label mb-1">
                <span class="label-text">From Date</span>
            </label>
            <x-mary-input
                wire:model.live="fromDate"
                type="date"
                icon="o-calendar"
                class="w-full" />
        </div>

        {{-- To Date --}}
        <div class="flex flex-col">
            <label class="label mb-1">
                <span class="label-text">To Date</span>
            </label>
            <x-mary-input
                wire:model.live="toDate"
                type="date"
                icon="o-calendar"
                class="w-full" />
        </div>
        </div>

        {{-- Clear Filters Button --}}
        <div class="flex justify-end mt-4">
            <x-mary-button
                wire:click="clearFilters"
                label="Clear Filters"
                icon="o-x-mark"
                class="btn-ghost btn-sm" />
        </div>
    </x-mary-card>

</div>