<?php

use Livewire\Volt\Component;
use App\Models\SavingsAccount;
use App\Models\SavingsAccountType;
use App\Models\Branch;
use App\Models\Customer;
use App\Enums\AccountStatusEnum;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $accountTypes = [];
    public $branches = [];

    public $account_number = '';
    public $account_type_id = '';
    public $branch_id = '';
    public $balance = 0;
    public $status = '';
    public $opened_date = '';
    public $customer_nics = [];
    public $nic_input = '';

    protected $rules = [
        'account_number' => 'required|string|max:256|unique:savings_account,account_number',
        'account_type_id' => 'required|exists:savings_account_type,id',
        'branch_id' => 'required|exists:branch,id',
        'balance' => 'required|numeric|min:0',
        'status' => 'required|in:ACTIVE,INACTIVE',
        'opened_date' => 'required|date',
        'customer_nics' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->accountTypes = SavingsAccountType::where('is_active', true)
            ->get()
            ->map(function ($type) {
                return ['id' => $type->id, 'name' => $type->name];
            })
            ->toArray();

        $this->branches = Branch::all()
            ->map(function ($branch) {
                return ['id' => $branch->id, 'name' => $branch->branch_name];
            })
            ->toArray();

        $this->status = AccountStatusEnum::ACTIVE->value;
        $this->opened_date = now()->format('Y-m-d');
    }

    public function addNic()
    {
        $this->validate([
            'nic_input' => 'required|string',
        ]);

        if (in_array($this->nic_input, $this->customer_nics)) {
            $this->addError('nic_input', 'This NIC has already been added.');
            return;
        }

        $this->customer_nics[] = $this->nic_input;
        $this->reset('nic_input');
    }

    public function removeNic($index)
    {
        unset($this->customer_nics[$index]);
        $this->customer_nics = array_values($this->customer_nics);
    }

    public function submit()
    {
        $this->validate();

        $nic_numbers = array_unique($this->customer_nics);

        if (empty($nic_numbers)) {
            $this->addError('customer_nics', 'Please enter at least one NIC number.');
            return;
        }

        $customers = Customer::whereIn('id_number', $nic_numbers)->get();

        if ($customers->count() !== count($nic_numbers)) {
            $found_nics = $customers->pluck('id_number')->toArray();
            $not_found = array_diff($nic_numbers, $found_nics);
            $this->addError('customer_nics', 'The following NIC numbers could not be found: ' . implode(', ', $not_found));
            return;
        }

        DB::transaction(function () use ($customers) {
            $account = SavingsAccount::create([
                'account_number' => $this->account_number,
                'account_type_id' => $this->account_type_id,
                'branch_id' => $this->branch_id,
                'balance' => $this->balance,
                'status' => $this->status,
                'opened_date' => $this->opened_date,
            ]);

            $account->customers()->attach($customers->pluck('id')->toArray());
        });

        $this->dispatch('toast', title: 'Savings account created successfully.');

        $this->reset(['account_number', 'account_type_id', 'branch_id', 'balance', 'status', 'opened_date', 'customer_nics', 'nic_input']);
    }
}; ?>

<x-mary-form wire:submit="submit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Account Number -->
        {{-- <x-mary-input label="Account Number" wire:model="account_number" readonly /> --}}

        <!-- Account Type -->
        <x-mary-select label="Account Type" wire:model="account_type_id"
            :options="$this->accountTypes"
            required />

        <!-- Branch -->
        <x-mary-select label="Branch" wire:model="branch_id"
            :options="$this->branches"
            required />

        <!-- Initial Balance -->
        <x-mary-input label="Initial Deposit" wire:model="balance" type="number" step="0.01" min="0" required />


        <!-- Opened Date -->
        <x-mary-input label="Opened Date" wire:model="opened_date" type="date" required />

        <!-- Customers NICs -->
        <div class="md:col-span-2">
            <label class="label">
                <span class="label-text">Customer NICs</span>
            </label>
            <div class="flex items-center gap-2">
                <x-mary-input placeholder="Enter NIC and press Add" wire:model="nic_input" wire:keydown.enter.prevent="addNic" class="flex-grow" />
                <x-mary-button label="Add" wire:click.prevent="addNic" class="btn-primary" />
            </div>
            @error('nic_input') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

            <div class="mt-4 space-y-2">
                @foreach ($customer_nics as $index => $nic)
                    <x-mary-list-item :item="['name' => $nic]" no-separator>
                        <x-slot:actions>
                            <x-mary-button icon="o-trash" wire:click.prevent="removeNic({{ $index }})" class="btn-sm btn-ghost text-red-500" />
                        </x-slot:actions>
                    </x-mary-list-item>
                @endforeach
            </div>
             @error('customer_nics') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
    </div>

    <!-- Button -->
    <x-slot:actions>
        <x-mary-button
            label="Create Savings Account"
            class="btn-primary"
            type="submit" />
    </x-slot:actions>
</x-mary-form>
