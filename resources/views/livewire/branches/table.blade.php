<?php

use App\Models\Branch;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'branch_code';
    public string $sortDirection = 'asc';

    #[On('filters-updated')]
    public function updateFilters($filters): void
    {
        $this->search = $filters['search'] ?? '';
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Branch::query();

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('branch_code', 'like', '%' . $this->search . '%')
                    ->orWhere('branch_name', 'like', '%' . $this->search . '%')
                    ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $branches = $query->paginate(60);

        return [
            'branches' => $branches,
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
                ['key' => 'branch_code', 'label' => 'Branch Code', 'class' => 'font-semibold'],
                ['key' => 'branch_name', 'label' => 'Branch Name', 'class' => 'font-semibold'],
                ['key' => 'address', 'label' => 'Address', 'class' => 'font-semibold'],
                ['key' => 'city', 'label' => 'City', 'class' => 'font-semibold'],
                ['key' => 'postal_code', 'label' => 'Postal Code', 'class' => 'font-semibold'],
                ['key' => 'phone', 'label' => 'Phone', 'class' => 'font-semibold'],
            ]" :rows="$branches">
                @scope('cell_branch_code', $branch)
                    <span class="font-mono">{{ $branch->branch_code }}</span>
                @endscope

                @scope('cell_branch_name', $branch)
                    <span class="font-semibold">{{ $branch->branch_name }}</span>
                @endscope

                @scope('cell_address', $branch)
                    {{ $branch->address }}
                @endscope

                @scope('cell_city', $branch)
                    {{ $branch->city }}
                @endscope

                @scope('cell_postal_code', $branch)
                    {{ $branch->postal_code }}
                @endscope

                @scope('cell_phone', $branch)
                    {{ $branch->phone }}
                @endscope
            </x-mary-table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $branches->links() }}
        </div>
    </x-mary-card>
</div>