<?php

use App\Models\Customer;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?int $branchFilter = null;
    public ?string $genderFilter = null;
    public ?int $statusFilter = null;
    public string $sortBy = 'first_name';
    public string $sortDirection = 'asc';

    #[On('filters-updated')]
    public function updateFilters($filters): void
    {
        $this->search = $filters['search'] ?? '';
        $this->branchFilter = $filters['branchFilter'] ?? null;
        $this->genderFilter = $filters['genderFilter'] ?? null;
        $this->statusFilter = $filters['statusFilter'] ?? null;
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Customer::query()
            ->with(['branch', 'status', 'employee']);

        // Role-based filtering: Agents can only see their assigned customers
        if (auth()->user()->canOnlyManageAssignedCustomers()) {
            $query->where('employee_id', auth()->id());
        }

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Branch filter
        if ($this->branchFilter) {
            $query->where('branch_id', $this->branchFilter);
        }

        // Gender filter
        if ($this->genderFilter) {
            $query->where('gender', $this->genderFilter);
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status_id', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $customers = $query->paginate(60);

        return [
            'customers' => $customers,
        ];
    }

    public function sortByColumn(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
}; ?>

<div>
    <x-mary-card shadow>
        <div class="overflow-x-auto">
            <x-mary-table :headers="[
                ['key' => 'name', 'label' => 'Name', 'class' => 'font-semibold'],
                ['key' => 'email', 'label' => 'Email', 'class' => 'font-semibold'],
                ['key' => 'phone', 'label' => 'Phone', 'class' => 'font-semibold'],
                ['key' => 'gender', 'label' => 'Gender', 'class' => 'font-semibold'],
                ['key' => 'branch', 'label' => 'Branch', 'class' => 'font-semibold'],
                ['key' => 'status', 'label' => 'Status', 'class' => 'font-semibold'],
                ['key' => 'created_at', 'label' => 'Created Date', 'class' => 'font-semibold'],
            ]" :rows="$customers">
                @scope('cell_name', $customer)
                    <a href="{{ route('customers.details', $customer) }}" class="font-bold text-primary underline">{{ $customer->first_name }} {{ $customer->last_name }}</a>
                @endscope

                @scope('cell_email', $customer)
                    <span class="text-primary">{{ $customer->email }}</span>
                @endscope

                @scope('cell_phone', $customer)
                    {{ $customer->phone }}
                @endscope

                @scope('cell_gender', $customer)
                    <span class="badge badge-info badge-soft">{{ $customer->gender->name }}</span>
                @endscope

                @scope('cell_branch', $customer)
                    {{ $customer->branch->branch_name }}
                @endscope

                @scope('cell_status', $customer)
                    @if($customer->status->status_name === 'ADULT')
                        <span class="badge badge-success badge-soft">{{ $customer->status->status_name }}</span>
                    @elseif($customer->status->status_name === 'SENIOR')
                        <span class="badge badge-warning badge-soft">{{ $customer->status->status_name }}</span>
                    @else
                        <span class="badge badge-error badge-soft">{{ $customer->status->status_name }}</span>
                    @endif
                @endscope

                @scope('cell_created_at', $customer)
                    {{ $customer->created_at->format('M d, Y') }}
                @endscope
            </x-mary-table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </x-mary-card>
</div>