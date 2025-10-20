<?php


use Livewire\Volt\Component;
use App\Models\Employee;

new class extends Component
{
    public Employee $employee;
    public array $customers = [];

    // Livewire will pass the Employee model if you render the component with :employee="$employee"
    public function mount(Employee $employee): void
    {
        $this->employee = $employee->load('customers.branch');

        $this->customers = $this->employee->customers->map(function ($customer) {
            return [
                'name' => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
                'phone' => $customer->phone ?? '',
                'email' => $customer->email ?? '',
                'id_number' => $customer->id_number ?? '',
                'address' => $customer->address ?? '',
                'city' => $customer->city ?? '',
                'branch' => $customer->branch->branch_name ?? 'N/A',
                'created_at' => $customer->created_at ? $customer->created_at->format('Y-m-d') : 'N/A',
            ];
        })->toArray();
    }

    
    
};
?>



<div class="space-y-6">

    <h2 class="text-2xl font-bold mb-6 text-center">
        Customers of {{ $employee->first_name }} {{ $employee->last_name }}
    </h2>

    {{-- Customer Table --}}
    @if (empty($customers) || count($customers) === 0)
        <p class="text-gray-500 text-center">No customers Yet.</p>
    @else
        <x-mary-table
            :headers="[
                ['key' => 'name', 'label' => 'Customer Name'],
                ['key' => 'phone', 'label' => 'Phone'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'id_number', 'label' => 'ID Number'],
                ['key' => 'address', 'label' => 'Address'],
                ['key' => 'city', 'label' => 'City'],
                ['key' => 'branch', 'label' => 'Branch'],
                ['key' => 'created_at', 'label' => 'Created At'],
            ]"
            :rows="$customers"
            striped
            hover
        />
    @endif

    {{-- Back Button --}}
    <div class="mt-4 text-center">
        <x-mary-button
            onclick="window.location='{{ route('reports.emp') }}'"
            class="bg-blue-400 hover:bg-gray-400 text-white px-6 py-0"
            color="gray"
            size="sm">
            Back to Agents
        </x-mary-button>
    </div>

</div>
