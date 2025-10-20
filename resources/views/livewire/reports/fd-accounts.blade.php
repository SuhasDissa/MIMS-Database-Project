<?php

use App\Models\FixedDeposit;
use App\Enums\FixedDepositStatusEnum;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;

new class extends Component {
    public $search = '';
    public $perPage = 10;

    public function mount(): void
    {
        // No initial data loading needed for Volt
    }

    public function with(): array
    {
        // Get active FD accounts with balances
        $activeFds = FixedDeposit::with(['customer', 'fdType', 'branch'])
            ->where('status', FixedDepositStatusEnum::ACTIVE)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('fd_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', function ($customerQuery) {
                          $customerQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('start_date', 'desc')
            ->paginate($this->perPage);

        // Calculate summary statistics
        $totalActiveFds = FixedDeposit::where('status', FixedDepositStatusEnum::ACTIVE)->count();
        $totalPrincipalAmount = FixedDeposit::where('status', FixedDepositStatusEnum::ACTIVE)->sum('principal_amount');
        $totalMaturityAmount = FixedDeposit::where('status', FixedDepositStatusEnum::ACTIVE)->sum('maturity_amount');

        return [
            'activeFds' => $activeFds,
            'totalActiveFds' => $totalActiveFds,
            'totalPrincipalAmount' => $totalPrincipalAmount,
            'totalMaturityAmount' => $totalMaturityAmount,
        ];
    }
}; ?>

<div class="space-y-6">
    {{-- Summary Statistics --}}
    <div class="grid gap-4 md:grid-cols-3">
        <x-mary-stat
            title="Active FD Accounts"
            :value="$totalActiveFds"
            icon="o-banknotes"
            color="text-success"
            class="bg-base-100 shadow-md" />

        <x-mary-stat
            title="Total Principal Amount"
            value="Rs. {{ number_format($totalPrincipalAmount, 2) }}"
            icon="o-currency-dollar"
            color="text-primary"
            class="bg-base-100 shadow-md" />

        <x-mary-stat
            title="Total Maturity Value"
            value="Rs. {{ number_format($totalMaturityAmount, 2) }}"
            icon="o-chart-bar"
            color="text-accent"
            class="bg-base-100 shadow-md" />
    </div>

    {{-- Filters --}}
    <x-mary-card title="Active Fixed Deposit Accounts" shadow separator>
        <div class="flex flex-col sm:flex-row gap-4 mb-4">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                placeholder="Search by FD number or customer name..."
                icon="o-magnifying-glass"
                class="flex-1" />

            <x-mary-select
                wire:model.live="perPage"
                :options="[['id' => 10, 'name' => '10 per page'], ['id' => 25, 'name' => '25 per page'], ['id' => 50, 'name' => '50 per page']]"
                placeholder="Items per page"
                class="w-full sm:w-48" />
        </div>

        {{-- FD Accounts Table --}}
        <div class="overflow-x-auto">
            <x-mary-table :headers="[
                ['key' => 'fd_number', 'label' => 'FD Number', 'class' => 'font-semibold'],
                ['key' => 'customer', 'label' => 'Customer', 'class' => 'font-semibold'],
                ['key' => 'fd_type', 'label' => 'FD Type', 'class' => 'font-semibold'],
                ['key' => 'branch', 'label' => 'Branch', 'class' => 'font-semibold'],
                ['key' => 'principal_amount', 'label' => 'Principal Amount', 'class' => 'font-semibold text-right'],
                ['key' => 'maturity_amount', 'label' => 'Maturity Amount', 'class' => 'font-semibold text-right'],
                ['key' => 'start_date', 'label' => 'Start Date', 'class' => 'font-semibold'],
                ['key' => 'maturity_date', 'label' => 'Maturity Date', 'class' => 'font-semibold'],
                ['key' => 'status', 'label' => 'Status', 'class' => 'font-semibold'],
            ]" :rows="$activeFds">

                @scope('cell_fd_number', $fd)
                    <span class="badge badge-primary badge-soft font-mono">{{ $fd->fd_number }}</span>
                @endscope

                @scope('cell_customer', $fd)
                    <div class="flex flex-col">
                        <span class="font-medium text-primary">{{ $fd->customer->first_name . ' ' . $fd->customer->last_name }}</span>
                        <span class="text-xs text-base-content/60">{{ $fd->customer->id_number }}</span>
                    </div>
                @endscope

                @scope('cell_fd_type', $fd)
                    <span class="badge badge-info badge-soft">{{ $fd->fdType->name }}</span>
                @endscope

                @scope('cell_branch', $fd)
                    <span class="text-base-content/80">{{ $fd->branch->branch_name }}</span>
                @endscope

                @scope('cell_principal_amount', $fd)
                    <span class="font-semibold text-primary text-right block">Rs. {{ number_format($fd->principal_amount, 2) }}</span>
                @endscope

                @scope('cell_maturity_amount', $fd)
                    <span class="font-semibold text-success text-right block">Rs. {{ number_format($fd->maturity_amount, 2) }}</span>
                @endscope

                @scope('cell_start_date', $fd)
                    <span class="text-base-content/70">{{ $fd->start_date->format('M d, Y') }}</span>
                @endscope

                @scope('cell_maturity_date', $fd)
                    <span class="text-base-content/70">{{ $fd->maturity_date->format('M d, Y') }}</span>
                @endscope

                @scope('cell_status', $fd)
                    <span class="badge badge-success badge-soft">{{ $fd->status->label() }}</span>
                @endscope

            </x-mary-table>
        </div>

        {{-- Pagination --}}
        @if($activeFds->hasPages())
            <div class="mt-4">
                {{ $activeFds->links() }}
            </div>
        @endif
    </x-mary-card>
</div>