<?php

namespace App\Livewire;

use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Enums\TransactionTypeEnum;
use Livewire\Component;

class SavingsTransactionForm extends Component
{
    public $type;
    public $from_id;
    public $to_id;
    public $amount;
    public $description;

    public array $accounts = [];
    public array $transactionTypes = [];

    public function mount()
    {
        $this->accounts = SavingsAccount::with('customers')->get()->map(function ($account) {
            $name = $account->customers->isNotEmpty() ? ' - ' . $account->customers->first()->name : '';
            return ['id' => $account->id, 'name' => $account->id . $name];
        })->toArray();

        $this->transactionTypes = collect([
            TransactionTypeEnum::DEPOSIT,
            TransactionTypeEnum::WITHDRAWAL,
            TransactionTypeEnum::TRANSFER,
        ])->map(fn ($enum) => ['id' => $enum->value, 'name' => $enum->value])->toArray();

        $this->type = TransactionTypeEnum::DEPOSIT->value;
    }

    public function save()
    {
        $this->validate([
            'type' => 'required|in:' . implode(',', array_column($this->transactionTypes, 'id')),
            'from_id' => 'nullable|required_if:type,' . TransactionTypeEnum::WITHDRAWAL->value . ',' . TransactionTypeEnum::TRANSFER->value . '|exists:savings_account,id',
            'to_id' => 'nullable|required_if:type,' . TransactionTypeEnum::DEPOSIT->value . ',' . TransactionTypeEnum::TRANSFER->value . '|exists:savings_account,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        SavingsTransaction::create([
            'type' => $this->type,
            'from_id' => $this->type === TransactionTypeEnum::DEPOSIT->value ? null : $this->from_id,
            'to_id' => $this->type === TransactionTypeEnum::WITHDRAWAL->value ? null : $this->to_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'status' => 'COMPLETED', // Assuming default status
            'balance_before' => 0, // Placeholder
            'balance_after' => 0, // Placeholder
        ]);

        $this->dispatch('toast', title: 'Savings transaction successfully created.');

        $this->reset();
    }

    public function render()
    {
        return view('livewire.savings-transaction-form');
    }
}
