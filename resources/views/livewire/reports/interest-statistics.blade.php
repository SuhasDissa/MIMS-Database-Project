<?php

use App\Models\SavingsAccountInterestCalculation;
use App\Models\SavingsAccount;
use App\Models\SavingsAccountType;
use App\Enums\CustomerStatusEnum;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;

new class extends Component {
    public array $customerStatusChart = [];
    public array $accountTypeChart = [];
    public array $monthlyTrendChart = [];

    public function mount(): void
    {
        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        $driver = DB::connection()->getDriverName();

        // Get interest distribution by customer status using database function/view
        if ($driver === 'pgsql') {
            $interestByCustomerStatus = collect(DB::select('SELECT * FROM get_interest_by_customer_status()'))
                ->map(function ($item) {
                    return [
                        'name' => CustomerStatusEnum::from($item->status_name)->label(),
                        'value' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        } else {
            $interestByCustomerStatus = collect(DB::select('SELECT * FROM vw_interest_by_customer_status'))
                ->map(function ($item) {
                    return [
                        'name' => CustomerStatusEnum::from($item->status_name)->label(),
                        'value' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        }

        // Get total interest paid per account type using database function/view
        if ($driver === 'pgsql') {
            $interestByAccountType = collect(DB::select('SELECT * FROM get_interest_by_account_type()'))
                ->map(function ($item) {
                    return [
                        'name' => $item->type_name,
                        'value' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        } else {
            $interestByAccountType = collect(DB::select('SELECT * FROM vw_interest_by_account_type'))
                ->map(function ($item) {
                    return [
                        'name' => $item->type_name,
                        'value' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        }

        // Get monthly interest trends using database function/view
        if ($driver === 'pgsql') {
            $monthlyInterest = collect(DB::select('SELECT * FROM get_monthly_interest_trends(12)'))
                ->map(function ($item) {
                    return [
                        'month' => date('M Y', strtotime($item->month_year . '-01')),
                        'interest' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        } else {
            $monthlyInterest = collect(DB::select('SELECT * FROM vw_monthly_interest_trends'))
                ->map(function ($item) {
                    return [
                        'month' => date('M Y', strtotime($item->month_year . '-01')),
                        'interest' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        }

        // Prepare chart data
        $this->customerStatusChart = [
            'type' => 'pie',
            'data' => [
                'labels' => array_column($interestByCustomerStatus, 'name'),
                'datasets' => [
                    [
                        'label' => 'Interest Distribution',
                        'data' => array_column($interestByCustomerStatus, 'value'),
                    ]
                ]
            ]
        ];

        $this->accountTypeChart = [
            'type' => 'bar',
            'data' => [
                'labels' => array_column($interestByAccountType, 'name'),
                'datasets' => [
                    [
                        'label' => 'Total Interest (Rs.)',
                        'data' => array_column($interestByAccountType, 'value'),
                    ]
                ]
            ]
        ];

        $this->monthlyTrendChart = [
            'type' => 'line',
            'data' => [
                'labels' => array_column($monthlyInterest, 'month'),
                'datasets' => [
                    [
                        'label' => 'Monthly Interest (Rs.)',
                        'data' => array_column($monthlyInterest, 'interest'),
                        'fill' => false,
                    ]
                ]
            ]
        ];
    }

    public function with(): array
    {
        // Calculate summary statistics
        $totalInterestPaid = SavingsAccountInterestCalculation::where('status', 'CREDITED')->sum('interest_amount');
        $totalInterestPending = SavingsAccountInterestCalculation::where('status', 'CALCULATED')->sum('interest_amount');
        $totalCalculations = SavingsAccountInterestCalculation::where('status', 'CREDITED')->count();

        $driver = DB::connection()->getDriverName();

        // Get interest distribution by customer status using database function/view
        if ($driver === 'pgsql') {
            $interestByCustomerStatus = collect(DB::select('SELECT * FROM get_interest_by_customer_status()'))
                ->map(function ($item) {
                    return [
                        'name' => CustomerStatusEnum::from($item->status_name)->label(),
                        'value' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        } else {
            $interestByCustomerStatus = collect(DB::select('SELECT * FROM vw_interest_by_customer_status'))
                ->map(function ($item) {
                    return [
                        'name' => CustomerStatusEnum::from($item->status_name)->label(),
                        'value' => (float) $item->total_interest
                    ];
                })
                ->toArray();
        }

        // Average interest rate by customer status using database function/view
        if ($driver === 'pgsql') {
            $avgInterestRates = collect(DB::select('SELECT * FROM get_avg_interest_rates_by_status()'))
                ->map(function ($item) {
                    return [
                        'status' => CustomerStatusEnum::from($item->status_name)->label(),
                        'rate' => (float) $item->avg_rate
                    ];
                })
                ->toArray();
        } else {
            $avgInterestRates = collect(DB::select('SELECT * FROM vw_avg_interest_rates_by_status'))
                ->map(function ($item) {
                    return [
                        'status' => CustomerStatusEnum::from($item->status_name)->label(),
                        'rate' => (float) $item->avg_rate
                    ];
                })
                ->toArray();
        }

        // Top 5 accounts by interest earned using database function/view
        if ($driver === 'pgsql') {
            $topAccounts = DB::select('SELECT * FROM get_top_accounts_by_interest(5)');
        } else {
            $topAccounts = DB::select('SELECT * FROM vw_top_accounts_by_interest');
        }

        return [
            'totalInterestPaid' => $totalInterestPaid,
            'totalInterestPending' => $totalInterestPending,
            'totalCalculations' => $totalCalculations,
            'avgInterestRates' => $avgInterestRates,
            'topAccounts' => $topAccounts,
            'interestByCustomerStatus' => $interestByCustomerStatus,
        ];
    }
}; ?>

<div class="space-y-6">
    {{-- Summary Statistics --}}
    <div class="grid gap-4 md:grid-cols-3">
        <x-mary-stat
            title="Total Interest Paid"
            value="Rs. {{ number_format($totalInterestPaid, 2) }}"
            icon="o-banknotes"
            color="text-success"
            class="bg-base-100 shadow-md" />

        <x-mary-stat
            title="Pending Interest"
            value="Rs. {{ number_format($totalInterestPending, 2) }}"
            icon="o-clock"
            color="text-warning"
            class="bg-base-100 shadow-md" />

        <x-mary-stat
            title="Total Calculations"
            :value="$totalCalculations"
            icon="o-calculator"
            color="text-primary"
            class="bg-base-100 shadow-md" />
    </div>

    {{-- Main Charts Grid --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Interest Distribution by Customer Status --}}
        <x-mary-card title="Interest Distribution by Customer Status" shadow separator>
            <x-mary-chart wire:model="customerStatusChart" class="w-128"/>

            {{-- Legend with percentages --}}
            <div class="mt-4 space-y-2">
                @php
                    $total = array_sum(array_column($interestByCustomerStatus, 'value'));
                @endphp
                @foreach($interestByCustomerStatus as $item)
                    @php
                        $percentage = $total > 0 ? ($item['value'] / $total) * 100 : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium">{{ $item['name'] }}</span>
                        <div class="text-right">
                            <span class="text-sm text-success font-semibold">Rs. {{ number_format($item['value'], 2) }}</span>
                            <span class="text-xs text-gray-500 ml-2">({{ number_format($percentage, 1) }}%)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-mary-card>

        {{-- Interest by Account Type --}}
        <x-mary-card title="Interest by Account Type" shadow separator>
            <x-mary-chart wire:model="accountTypeChart" />
        </x-mary-card>
    </div>

    {{-- Monthly Interest Trend --}}
    <x-mary-card title="Monthly Interest Trend (Last 12 Months)" shadow separator>
        <x-mary-chart wire:model="monthlyTrendChart" />
    </x-mary-card>

    {{-- Additional Insights Grid --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Average Interest Rates by Customer Status --}}
        <x-mary-card title="Interest Rates by Customer Status" shadow separator>
            <div class="space-y-4">
                @foreach($avgInterestRates as $rate)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-primary"></div>
                            <span class="font-medium">{{ $rate['status'] }}</span>
                        </div>
                        <span class="badge badge-primary badge-lg">{{ number_format($rate['rate'] * 100, 1) }}%</span>
                    </div>
                @endforeach
            </div>
        </x-mary-card>

        {{-- Top Accounts by Interest Earned --}}
        <x-mary-card title="Top 5 Accounts by Interest Earned" shadow separator>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="font-semibold">Rank</th>
                            <th class="font-semibold">Account Number</th>
                            <th class="font-semibold text-right">Total Interest</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topAccounts as $index => $account)
                            <tr>
                                <td>
                                    @if($index === 0)
                                        <span class="badge badge-warning">🥇</span>
                                    @elseif($index === 1)
                                        <span class="badge badge-ghost">🥈</span>
                                    @elseif($index === 2)
                                        <span class="badge badge-ghost">🥉</span>
                                    @else
                                        <span class="badge badge-ghost">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="font-mono text-primary">{{ $account->account_number }}</td>
                                <td class="text-right text-success font-semibold">Rs. {{ number_format($account->total_interest, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-mary-card>
    </div>

    {{-- Key Insights --}}
    <x-mary-card title="Key Insights" shadow separator>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="flex items-start gap-3 p-4 bg-base-200 rounded-lg">
                <x-mary-icon name="o-light-bulb" class="w-6 h-6 text-warning" />
                <div>
                    <div class="text-sm font-semibold">Total Interest Impact</div>
                    <div class="text-xs text-gray-600 mt-1">
                        Rs. {{ number_format($totalInterestPaid + $totalInterestPending, 2) }} committed to customers
                    </div>
                </div>
            </div>

            @php
                $seniorInterest = collect($interestByCustomerStatus)->firstWhere('name', 'Senior')['value'] ?? 0;
                $totalDistributed = array_sum(array_column($interestByCustomerStatus, 'value'));
            @endphp

            <div class="flex items-start gap-3 p-4 bg-base-200 rounded-lg">
                <x-mary-icon name="o-chart-bar" class="w-6 h-6 text-accent" />
                <div>
                    <div class="text-sm font-semibold">Average per Calculation</div>
                    <div class="text-xs text-gray-600 mt-1">
                        Rs. {{ $totalCalculations > 0 ? number_format($totalInterestPaid / $totalCalculations, 2) : '0.00' }} per interest credit
                    </div>
                </div>
            </div>
        </div>
    </x-mary-card>
</div>
