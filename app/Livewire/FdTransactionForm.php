<?php

namespace App\Livewire;

use App\Models\FixedDeposit;
use App\Models\FdTransaction;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionMethodEnum;
use Livewire\Component;

class FdTransactionForm extends Component
{
    public $type;
    public $method;
    public $fd_acc_id;
    public $amount;
    public $description;

    public array $accounts = [];
    public array $transactionTypes = [];
    public array $transactionMethods = [];

    public function mount()
    {
        $this->accounts = FixedDeposit::with('customer')->get()->map(function ($account) {
            $name = $account->customer ? ' - ' . $account->customer->name : '';
            return ['id' => $account->id, 'name' => $account->id . $name];
        })->toArray();

        $this->transactionTypes = collect([
            TransactionTypeEnum::DEPOSIT,
            TransactionTypeEnum::WITHDRAWAL,
        ])->map(fn ($enum) => ['id' => $enum->value, 'name' => $enum->value])->toArray();

        $this->transactionMethods = collect([
            TransactionMethodEnum::ACCOUNT,
            TransactionMethodEnum::CASH,
        ])->map(fn ($enum) => ['id' => $enum->value, 'name' => $enum->value])->toArray();

        $this->type = TransactionTypeEnum::DEPOSIT->value;
        $this->method = TransactionMethodEnum::CASH->value;
    }

    public function save()
    {
        $this->validate([
            'type' => 'required|in:' . implode(',', array_column($this->transactionTypes, 'id')),
            'method' => 'required|in:' . implode(',', array_column($this->transactionMethods, 'id')),
            'fd_acc_id' => 'required|exists:fixed_deposits,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        FdTransaction::create([
            'type' => $this->type,
            'method' => $this->method,
            'fd_acc_id' => $this->fd_acc_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'balance_before' => 0, // Placeholder
            'balance_after' => 0, // Placeholder
        ]);

        $this->dispatch('toast', title: 'FD transaction successfully created.');

        $this->reset();
    }

    public function render()
    {
        return view('livewire.fd-transaction-form');
    }
}
