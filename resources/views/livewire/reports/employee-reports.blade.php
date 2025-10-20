<?php

use Livewire\Volt\Component;
use App\Models\Employee;
use App\Models\Branch;

new class extends Component {
    public $employees = [];
    public $totalEmployees = 0;
    public $totalCustomers = 0;
    public $totalBranches = 0;

    public function mount(): void
    {
        $this->loadEmployees();
        $this->totalBranches = Branch::count();
    }

    public function loadEmployees(): void
    {
        $employees = Employee::with('branch')
            ->withCount('customers')
            ->get();

        $this->totalEmployees = $employees->count();
        $this->totalCustomers = $employees->sum('customers_count');

        $this->employees = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')),
                'email' => $employee->email ?? '',
                'phone' => $employee->phone ?? '',
                'nic_num' => $employee->nic_num ?? '',
                'branch' => $employee->branch->branch_name ?? '',
                'created_at' => $employee->created_at ? $employee->created_at->format('Y-m-d') : 'N/A',
                'customers_count' => $employee->customers_count ?? 0, 
            ];
        })->toArray();
    }
};
?>


<div class="space-y-8 font-sans">

    {{-- Summary Stats --}}
    <div class="grid gap-4 md:grid-cols-3 mb-6">
        <x-mary-stat
            title="Active Agents"
            :value="$totalEmployees"
            icon="o-users"
            color="text-success"
            class="bg-base-100 shadow-md w-24" />

        <x-mary-stat
            title="Total Customers"
            :value="$totalCustomers"
            icon="o-users"
            color="text-primary"
            class="bg-base-100 shadow-md w-24" />

        <x-mary-stat
            title="Total Branches"
            :value="$totalBranches"
            icon="o-banknotes"
            color="red-500"
            class="bg-base-100 shadow-md w-24" />
    </div>

    {{-- Employee Table --}}
    <x-mary-table
        :headers="[
            ['key' => 'id', 'label' => 'Number'],
            ['key' => 'name', 'label' => 'Agent Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'phone', 'label' => 'Phone'],
            ['key' => 'nic_num', 'label' => 'NIC Number'],
            ['key' => 'branch', 'label' => 'Branch'],
            ['key' => 'created_at', 'label' => 'Joined At'],
            ['key' => 'action', 'label' => 'View Customers'],
        ]"
        :rows="$employees"
        striped
    >
        @scope('cell_action', $row)
            <x-mary-button
                tag="button"
                color="blue"
                size="md"
                class="bg-blue-400 hover:bg-gray-400 text-white px-3 py-0"
                onclick="window.location='{{ route('reports.empcus', ['employee' => $row['id']]) }}'">
                {{ $row['customers_count'] ?? 0 }} Customers
            </x-mary-button>
        @endscope
    </x-mary-table>

</div>
