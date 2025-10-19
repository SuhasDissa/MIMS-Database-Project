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
                :options="[10 => '10 per page', 25 => '25 per page', 50 => '50 per page']"
                placeholder="Items per page"
                class="w-full sm:w-48" />
        </div>

        {{-- FD Accounts Table --}}
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th class="font-semibold">FD Number</th>
                        <th class="font-semibold">Customer</th>
                        <th class="font-semibold">FD Type</th>
                        <th class="font-semibold">Branch</th>
                        <th class="font-semibold text-right">Principal Amount</th>
                        <th class="font-semibold text-right">Maturity Amount</th>
                        <th class="font-semibold">Start Date</th>
                        <th class="font-semibold">Maturity Date</th>
                        <th class="font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeFds as $fd)
                        <tr class="hover">
                            <td class="font-mono text-primary">{{ $fd->fd_number }}</td>
                            <td>
                                <div class="font-medium">{{ $fd->customer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $fd->customer->nic }}</div>
                            </td>
                            <td>{{ $fd->fdType->name }}</td>
                            <td>{{ $fd->branch->name }}</td>
                            <td class="text-right font-semibold">Rs. {{ number_format($fd->principal_amount, 2) }}</td>
                            <td class="text-right font-semibold text-success">Rs. {{ number_format($fd->maturity_amount, 2) }}</td>
                            <td>{{ $fd->start_date->format('M d, Y') }}</td>
                            <td>{{ $fd->maturity_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge badge-success">{{ $fd->status->label() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                No active FD accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($activeFds->hasPages())
            <div class="mt-4">
                {{ $activeFds->links() }}
            </div>
        @endif
    </x-mary-card>
</div>