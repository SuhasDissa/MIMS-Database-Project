<?php

use App\Models\SavingsAccount;
use App\Models\SavingsAccountInterestCalculation;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public SavingsAccount $account;

    public function with(): array
    {
        $interestCalculations = SavingsAccountInterestCalculation::where('account_id', $this->account->id)
            ->orderBy('calculation_period_end', 'desc')
            ->paginate(12);

        $totalInterestEarned = SavingsAccountInterestCalculation::where('account_id', $this->account->id)
            ->where('status', 'CREDITED')
            ->sum('interest_amount');

        $pendingInterest = SavingsAccountInterestCalculation::where('account_id', $this->account->id)
            ->where('status', 'CALCULATED')
            ->sum('interest_amount');

        return [
            'interestCalculations' => $interestCalculations,
            'totalInterestEarned' => $totalInterestEarned,
            'pendingInterest' => $pendingInterest,
        ];
    }
}; ?>

<div>
    {{-- Summary Stats --}}
    <div class="grid gap-4 md:grid-cols-2 mb-6">
        <x-mary-stat
            title="Total Interest Earned"
            value="Rs. {{ number_format($totalInterestEarned, 2) }}"
            icon="o-banknotes"
            color="text-success"
            class="bg-base-100 shadow-md" />

        <x-mary-stat
            title="Pending Interest"
            value="Rs. {{ number_format($pendingInterest, 2) }}"
            icon="o-clock"
            color="text-warning"
            class="bg-base-100 shadow-md" />
    </div>

    {{-- Interest History Table --}}
    <x-mary-card title="Interest Calculation History" shadow separator>
        @if($interestCalculations->isEmpty())
            <div class="text-center py-12">
                <x-mary-icon name="o-calculator" class="w-16 h-16 mx-auto text-gray-300" />
                <p class="mt-4 text-gray-500">No interest calculations found for this account.</p>
            </div>
        @else
            @php
                $headers = [
                    ['key' => 'period', 'label' => 'Period'],
                    ['key' => 'days', 'label' => 'Days'],
                    ['key' => 'principal', 'label' => 'Principal'],
                    ['key' => 'rate', 'label' => 'Rate'],
                    ['key' => 'interest', 'label' => 'Interest'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'credited_date', 'label' => 'Credited Date'],
                ];
            @endphp

            <x-mary-table :headers="$headers" :rows="$interestCalculations" with-pagination>
                @scope('cell_period', $calc)
                    <div class="text-sm">
                        {{ $calc->calculation_period_start->format('M d, Y') }}
                    </div>
                    <div class="text-xs text-gray-500">
                        to {{ $calc->calculation_period_end->format('M d, Y') }}
                    </div>
                @endscope

                @scope('cell_days', $calc)
                    <span class="badge badge-soft badge-ghost badge-sm">{{ number_format($calc->days_calculated, 0) }} days</span>
                @endscope

                @scope('cell_principal', $calc)
                    <span class="text-sm">Rs. {{ number_format($calc->principal_amount, 2) }}</span>
                @endscope

                @scope('cell_rate', $calc)
                    <span class="text-sm font-semibold text-primary">{{ number_format($calc->interest_rate, 2) }}%</span>
                @endscope

                @scope('cell_interest', $calc)
                    <span class="font-semibold text-success">Rs. {{ number_format($calc->interest_amount, 2) }}</span>
                @endscope

                @scope('cell_status', $calc)
                    @if($calc->status->value === 'CREDITED')
                        <span class="badge badge-soft badge-success badge-sm">{{ $calc->status->name }}</span>
                    @elseif($calc->status->value === 'CALCULATED')
                        <span class="badge badge-soft badge-warning badge-sm">{{ $calc->status->name }}</span>
                    @else
                        <span class="badge badge-soft badge-error badge-sm">{{ $calc->status->name }}</span>
                    @endif
                @endscope

                @scope('cell_credited_date', $calc)
                    @if($calc->credited_date)
                        <span class="text-sm">{{ $calc->credited_date->format('M d, Y') }}</span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                @endscope
            </x-mary-table>
        @endif
    </x-mary-card>
</div>
