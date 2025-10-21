@php
    use App\Models\Customer;
    use App\Models\Branch;
    use App\Models\Employee;
    use App\Models\SavingsAccount;
    use App\Models\FixedDeposit;

    $totalCustomers = Customer::count();
    $totalBranches = Branch::count();
    $totalEmployees = Employee::count();
    $totalSavingsAccounts = SavingsAccount::count();
    $totalFixedDeposits = FixedDeposit::count();
    $totalSavingsBalance = SavingsAccount::sum('balance');
    $totalFdAmount = FixedDeposit::sum('principal_amount');
@endphp

<x-layouts.app>
    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <x-mary-header title="Dashboard" subtitle="Welcome to your banking management system" separator />
        </div>

        {{-- Summary Statistics --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <x-mary-stat
                title="Total Customers"
                :value="$totalCustomers"
                icon="o-users"
                color="text-primary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Total Branches"
                :value="$totalBranches"
                icon="o-building-office"
                color="text-secondary"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Employees"
                :value="$totalEmployees"
                icon="o-identification"
                color="text-accent"
                class="bg-base-100 shadow-md" />

            <x-mary-stat
                title="Savings Accounts"
                :value="$totalSavingsAccounts"
                icon="o-credit-card"
                color="text-info"
                class="bg-base-100 shadow-md" />
        </div>

        {{-- Financial Overview --}}
        <div class="grid gap-4 md:grid-cols-2">
            <x-mary-card title="Savings Accounts Overview" shadow separator>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Total Accounts</span>
                        <span class="text-2xl font-bold text-primary">{{ $totalSavingsAccounts }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Total Balance</span>
                        <span class="text-2xl font-bold text-success">Rs.{{ number_format($totalSavingsBalance, 2) }}</span>
                    </div>
                </div>
            </x-mary-card>

            <x-mary-card title="Fixed Deposits Overview" shadow separator>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Total FDs</span>
                        <span class="text-2xl font-bold text-primary">{{ $totalFixedDeposits }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium">Total Amount</span>
                        <span class="text-2xl font-bold text-success">Rs.{{ number_format($totalFdAmount, 2) }}</span>
                    </div>
                </div>
            </x-mary-card>
        </div>

        {{-- Quick Actions --}}
        <x-mary-card title="Quick Actions" shadow separator>
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                <x-mary-button
                    label="Create Customer"
                    icon="o-user-plus"
                    link="{{ route('create.customer') }}"
                    class="btn-primary" />

                <x-mary-button
                    label="Create Branch"
                    icon="o-building-office"
                    link="{{ route('create.branch') }}"
                    class="btn-secondary" />

                <x-mary-button
                    label="Create FD"
                    icon="o-banknotes"
                    link="{{ route('create.fd') }}"
                    class="btn-accent" />

                <x-mary-button
                    label="Create Employee"
                    icon="o-identification"
                    link="{{ route('create.employee') }}"
                    class="btn-info" />
            </div>
        </x-mary-card>

        {{-- Recent Activity --}}
        <x-mary-card title="System Information" shadow separator>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-check-circle" class="w-8 h-8 text-success" />
                    <div>
                        <div class="text-sm text-gray-500">System Status</div>
                        <div class="font-semibold">All Systems Operational</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-clock" class="w-8 h-8 text-info" />
                    <div>
                        <div class="text-sm text-gray-500">Last Updated</div>
                        <div class="font-semibold">{{ now()->format('M d, Y H:i') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-mary-icon name="o-user" class="w-8 h-8 text-accent" />
                    <div>
                        <div class="text-sm text-gray-500">Logged in as</div>
                        <div class="font-semibold">{{ auth()->user()->name }}</div>
                    </div>
                </div>
            </div>
        </x-mary-card>
    </div>
</x-layouts.app>
