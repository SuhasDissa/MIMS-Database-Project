<?php

use App\Models\SavingsAccount;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?int $accountTypeFilter = null;
    public ?int $branchFilter = null;
    public ?string $statusFilter = null;
    public string $sortBy = 'account_number';
    public string $sortDirection = 'asc';

    #[On('filters-updated')]
    public function updateFilters($filters): void
    {
        $this->search = $filters['search'] ?? '';
        $this->accountTypeFilter = $filters['accountTypeFilter'] ?? null;
        $this->branchFilter = $filters['branchFilter'] ?? null;
        $this->statusFilter = $filters['statusFilter'] ?? null;
        $this->resetPage();
    }

    public function with(): array
    {
        $query = SavingsAccount::query()
            ->with(['accountType', 'branch', 'customers']);

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('account_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customers', function ($customerQuery) {
                        $customerQuery->where('first_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Account type filter
        if ($this->accountTypeFilter) {
            $query->where('account_type_id', $this->accountTypeFilter);
        }

        // Branch filter
        if ($this->branchFilter) {
            $query->where('branch_id', $this->branchFilter);
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $accounts = $query->paginate(60);

        return [
            'accounts' => $accounts,
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
                ['key' => 'account_number', 'label' => 'Account Number', 'class' => 'font-semibold'],
                ['key' => 'customers', 'label' => 'Customer(s)', 'class' => 'font-semibold'],
                ['key' => 'account_type', 'label' => 'Account Type', 'class' => 'font-semibold'],
                ['key' => 'branch', 'label' => 'Branch', 'class' => 'font-semibold'],
                ['key' => 'balance', 'label' => 'Balance', 'class' => 'font-semibold'],
                ['key' => 'status', 'label' => 'Status', 'class' => 'font-semibold'],
                ['key' => 'opened_date', 'label' => 'Opened Date', 'class' => 'font-semibold'],
            ]" :rows="$accounts">
                @scope('cell_account_number', $account)
                    <a href="{{ route('accounts.details', $account->id) }}" class="text-primary font-mono hover:underline">
                        {{ $account->account_number }}
                    </a>
                @endscope

                @scope('cell_customers', $account)
                    <div class="flex flex-col gap-1">
                        @foreach($account->customers as $customer)
                            <span class="badge badge-ghost badge-sm">{{ $customer->first_name }}</span>
                        @endforeach
                    </div>
                @endscope

                @scope('cell_account_type', $account)
                    <span class="badge badge-primary badge-soft">{{ $account->accountType->name }}</span>
                @endscope

                @scope('cell_branch', $account)
                    {{ $account->branch->branch_name }}
                @endscope

                @scope('cell_balance', $account)
                    <span class="font-semibold text-success">Rs. {{ number_format($account->balance, 2) }}</span>
                @endscope

                @scope('cell_status', $account)
                    @if($account->status === 'ACTIVE')
                        <span class="badge badge-success badge-soft">{{ $account->status }}</span>
                    @elseif($account->status === 'DORMANT')
                        <span class="badge badge-warning badge-soft">{{ $account->status }}</span>
                    @else
                        <span class="badge badge-error badge-soft">{{ $account->status }}</span>
                    @endif
                @endscope

                @scope('cell_opened_date', $account)
                    {{ $account->opened_date->format('M d, Y') }}
                @endscope
            </x-mary-table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $accounts->links() }}
        </div>
    </x-mary-card>
</div>
