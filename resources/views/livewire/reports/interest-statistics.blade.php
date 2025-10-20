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
        // Get interest distribution by customer status (Child, Senior, Adult)
        $interestByCustomerStatus = DB::table('savings_account_interest_calculations as calc')
            ->join('savings_account as acc', 'calc.account_id', '=', 'acc.id')
            ->join('savings_account_type as type', 'acc.account_type_id', '=', 'type.id')
            ->join('customer_status_types as status', 'type.customer_status_id', '=', 'status.id')
            ->select('status.status_name', DB::raw('SUM(calc.interest_amount) as total_interest'))
            ->where('calc.status', 'CREDITED')
            ->groupBy('status.status_name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => CustomerStatusEnum::from($item->status_name)->label(),
                    'value' => (float) $item->total_interest
                ];
            })
            ->toArray();

        // Get total interest paid per account type
        $interestByAccountType = DB::table('savings_account_interest_calculations as calc')
            ->join('savings_account as acc', 'calc.account_id', '=', 'acc.id')
            ->join('savings_account_type as type', 'acc.account_type_id', '=', 'type.id')
            ->select('type.name', DB::raw('SUM(calc.interest_amount) as total_interest'))
            ->where('calc.status', 'CREDITED')
            ->groupBy('type.name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'value' => (float) $item->total_interest
                ];
            })
            ->toArray();

        // Get monthly interest trends (last 12 months)
        $monthlyInterest = SavingsAccountInterestCalculation::where('status', 'CREDITED')
            ->where('credited_date', '>=', now()->subMonths(12))
            ->whereNotNull('credited_date')
            ->get()
            ->groupBy(function ($item) {
                return $item->credited_date->format('Y-m');
            })
            ->map(function ($group) {
                return [
                    'month' => $group->first()->credited_date->format('M Y'),
                    'interest' => (float) $group->sum('interest_amount')
                ];
            })
            ->sortKeys()
            ->values()
            ->toArray();

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
                        'fill' => true,
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

        // Get interest distribution by customer status for legend
        $interestByCustomerStatus = DB::table('savings_account_interest_calculations as calc')
            ->join('savings_account as acc', 'calc.account_id', '=', 'acc.id')
            ->join('savings_account_type as type', 'acc.account_type_id', '=', 'type.id')
            ->join('customer_status_types as status', 'type.customer_status_id', '=', 'status.id')
            ->select('status.status_name', DB::raw('SUM(calc.interest_amount) as total_interest'))
            ->where('calc.status', 'CREDITED')
            ->groupBy('status.status_name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => CustomerStatusEnum::from($item->status_name)->label(),
                    'value' => (float) $item->total_interest
                ];
            })
            ->toArray();

        // Average interest rate by customer status
        $avgInterestRates = DB::table('savings_account_type as type')
            ->join('customer_status_types as status', 'type.customer_status_id', '=', 'status.id')
            ->select('status.status_name', DB::raw('AVG(type.interest_rate) as avg_rate'))
            ->groupBy('status.status_name')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => CustomerStatusEnum::from($item->status_name)->label(),
                    'rate' => (float) $item->avg_rate
                ];
            })
            ->toArray();

        // Top 5 accounts by interest earned
        $topAccounts = DB::table('savings_account_interest_calculations as calc')
            ->join('savings_account as acc', 'calc.account_id', '=', 'acc.id')
            ->select('acc.account_number', DB::raw('SUM(calc.interest_amount) as total_interest'))
            ->where('calc.status', 'CREDITED')
            ->groupBy('acc.id', 'acc.account_number')
            ->orderByDesc('total_interest')
            ->limit(5)
            ->get()
            ->toArray();

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
                $seniorPercentage = $totalDistributed > 0 ? ($seniorInterest / $totalDistributed) * 100 : 0;
            @endphp
            <div class="flex items-start gap-3 p-4 bg-base-200 rounded-lg">
                <x-mary-icon name="o-user-group" class="w-6 h-6 text-info" />
                <div>
                    <div class="text-sm font-semibold">Senior Citizen Benefits</div>
                    <div class="text-xs text-gray-600 mt-1">
                        {{ number_format($seniorPercentage, 1) }}% of total interest goes to seniors
                    </div>
                </div>
            </div>

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
