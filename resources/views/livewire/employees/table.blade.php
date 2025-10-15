<?php

use App\Models\Employee;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?int $branchFilter = null;
    public ?bool $statusFilter = null;
    public string $sortBy = 'first_name';
    public string $sortDirection = 'asc';

    #[On('filters-updated')]
    public function updateFilters($filters): void
    {
        $this->search = $filters['search'] ?? '';
        $this->branchFilter = $filters['branchFilter'] ?? null;
        $this->statusFilter = $filters['statusFilter'] ?? null;
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Employee::query()
            ->with(['branch']);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('nic_num', 'like', '%' . $this->search . '%');
            });
        }

        // Branch filter
        if ($this->branchFilter) {
            $query->where('branch_id', $this->branchFilter);
        }

        // Status filter
        if ($this->statusFilter !== null) {
            $query->where('is_active', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $employees = $query->paginate(60);

        return [
            'employees' => $employees,
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
                ['key' => 'position', 'label' => 'Position', 'class' => 'font-semibold'],
                ['key' => 'branch', 'label' => 'Branch', 'class' => 'font-semibold'],
                ['key' => 'status', 'label' => 'Status', 'class' => 'font-semibold'],
                ['key' => 'created_at', 'label' => 'Created Date', 'class' => 'font-semibold'],
            ]" :rows="$employees">
                @scope('cell_name', $employee)
                    <span class="font-semibold">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                @endscope

                @scope('cell_email', $employee)
                    <span class="text-primary">{{ $employee->email }}</span>
                @endscope

                @scope('cell_phone', $employee)
                    {{ $employee->phone }}
                @endscope

                @scope('cell_position', $employee)
                    <span class="badge badge-info badge-soft">{{ $employee->position }}</span>
                @endscope

                @scope('cell_branch', $employee)
                    {{ $employee->branch->branch_name }}
                @endscope

                @scope('cell_status', $employee)
                    @if($employee->is_active)
                        <span class="badge badge-success badge-soft">Active</span>
                    @else
                        <span class="badge badge-error badge-soft">Inactive</span>
                    @endif
                @endscope

                @scope('cell_created_at', $employee)
                    {{ $employee->created_at->format('M d, Y') }}
                @endscope
            </x-mary-table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </x-mary-card>
</div>