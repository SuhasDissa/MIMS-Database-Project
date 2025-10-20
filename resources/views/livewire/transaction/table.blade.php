<?php

use App\Models\SavingsTransaction;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?string $Type = null;
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public ?float $minAmount = null;
    public ?float $maxAmount = null;
    public string $sortBy = 'updated_at';
    public string $sortDirection = 'desc';

    #[On('filters-updated')]
    public function updateFilters($filters): void
    {
        $this->search = $filters['search'] ?? '';
        $this->Type = $filters['Type'] ?? null;
        $this->fromDate = $filters['fromDate'] ?? null;
        $this->toDate = $filters['toDate'] ?? null;
        $this->minAmount = $filters['minAmount'] ?? null;
        $this->maxAmount = $filters['maxAmount'] ?? null;
        $this->resetPage();

        
    }

    public function with(): array
    {
        $query = SavingsTransaction::query()->with(['fromAccount', 'toAccount']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('fromAccount', fn($a) => $a->where('account_number', 'like', "%{$this->search}%"))
                ->orWhereHas('toAccount', fn($a) => $a->where('account_number', 'like', "%{$this->search}%"))
                ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if (!is_null($this->Type) && $this->Type !== '') {
            $query->where('type', $this->Type);
        }

        if ($this->fromDate) {
            $query->whereDate('updated_at', '>=', $this->fromDate);
        }
        if ($this->toDate) {
            $query->whereDate('updated_at', '<=', $this->toDate);
        }

        if (!is_null($this->minAmount)) {
            $query->where('amount', '>=', $this->minAmount);
        }
        if (!is_null($this->maxAmount)) {
            $query->where('amount', '<=', $this->maxAmount);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $transactions = $query->paginate(50);

        return ['transactions' => $transactions];

    }
}; ?>


<div>
    <x-mary-card shadow>
        <div class="overflow-x-auto">
            <x-mary-table :headers="[
                ['key' => 'type', 'label' => 'Type', 'class' => 'font-semibold'],
                ['key' => 'from_account', 'label' => 'From Account', 'class' => 'font-semibold'],
                ['key' => 'to_account', 'label' => 'To Account', 'class' => 'font-semibold'],
                ['key' => 'amount', 'label' => 'Amount', 'class' => 'font-semibold'],
                ['key' => 'status', 'label' => 'Status', 'class' => 'font-semibold'],
                ['key' => 'description', 'label' => 'Description', 'class' => 'font-semibold'],
                ['key' => 'updated_at', 'label' => 'Updated At', 'class' => 'font-semibold'],
            ]" :rows="$transactions">

                @scope('cell_type', $transaction)
                    @if($transaction->type->value === 'DEPOSIT')
                        <span class="badge badge-success badge-soft">{{ $transaction->type->name }}</span>
                    @elseif($transaction->type->value === 'WITHDRAWAL')
                        <span class="badge badge-error badge-soft">{{ $transaction->type->name }}</span>
                    @else
                        <span class="badge badge-info badge-soft">{{ $transaction->type->name }}</span>
                    @endif
                @endscope

                @scope('cell_from_account', $transaction)
                    @if($transaction->fromAccount)
                        <a href="{{ route('accounts.details', $transaction->fromAccount->id) }}" class="font-mono text-primary underline">{{ $transaction->fromAccount->account_number }}</a>
                    @else
                        <span class="text-base-content/50">-</span>
                    @endif
                @endscope

                @scope('cell_to_account', $transaction)
                    @if($transaction->toAccount)
                        <a href="{{ route('accounts.details', $transaction->toAccount->id) }}" class="font-mono text-primary underline">{{ $transaction->toAccount->account_number }}</a>
                    @else
                        <span class="text-base-content/50">-</span>
                    @endif
                @endscope

                @scope('cell_amount', $transaction)
                    @if($transaction->type->value === 'DEPOSIT')
                        <span class="font-semibold text-success">+ Rs. {{ number_format($transaction->amount, 2) }}</span>
                    @elseif($transaction->type->value === 'WITHDRAWAL')
                        <span class="font-semibold text-error">- Rs. {{ number_format($transaction->amount, 2) }}</span>
                    @else
                        <span class="font-semibold text-info">Rs. {{ number_format($transaction->amount, 2) }}</span>
                    @endif
                @endscope

                @scope('cell_status', $transaction)
                    @if($transaction->status->value === 'completed')
                        <span class="badge badge-success badge-soft">{{ $transaction->status->name }}</span>
                    @elseif($transaction->status->value === 'pending')
                        <span class="badge badge-warning badge-soft">{{ $transaction->status->name }}</span>
                    @else
                        <span class="badge badge-error badge-soft">{{ $transaction->status->name }}</span>
                    @endif
                @endscope

                @scope('cell_description', $transaction)
                    {{ $transaction->description }}
                @endscope

                @scope('cell_updated_at', $transaction)
                    {{ $transaction->updated_at->format('M d, Y') }}
                @endscope

            </x-mary-table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </x-mary-card>
</div>
