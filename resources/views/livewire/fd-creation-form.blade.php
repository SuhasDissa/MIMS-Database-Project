<?php

use App\Models\SavingsAccount;
use App\Models\FixedDeposit;
use App\Models\FixedDepositType;
use App\Models\Branch;
use Livewire\Volt\Component;

new class extends Component {
    public $fd_number;
    public $customer_id;
    public $fd_type_id;
    public $linked_account_id;
    public $branch_id;
    public $principal_amount;
    public $interest_freq = null;
    public $interest_payout_option = null;
    public $auto_renewal = null;

    public $fdTypeOptions = [];

    public $interestFrequencyOptions = [
        ['name' => 'Monthly', 'id' => 'MONTHLY'],
        ['name' => 'At Maturity', 'id' => 'END'],
    ];

    public $interestPayoutOptions = [
        ['name' => 'Transfer to Savings', 'id' => 'TRANSFER_TO_SAVINGS'],
        ['name' => 'Renew Fixed Deposit', 'id' => 'RENEW_FD'],
    ];

    public $autoRenewalOptions = [
        ['name' => 'Yes', 'id' => true],
        ['name' => 'No', 'id' => false],
    ];

    public $branchOptions = [];

    // Linked savings account dropdown
    public $linkedAccountOptions = [];

    public function mount()
    {
        // Load FD types
        $this->fdTypeOptions = \App\Models\FixedDepositType::where('is_active', true)
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name . ' (' . $type->tenure_months . ' months @ ' . $type->interest_rate . '%)',
                ];
            })
            ->toArray();

        // Load branches
        $this->branchOptions = \App\Models\Branch::all()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->branch_name,
                ];
            })
            ->toArray();

        $this->linkedAccountOptions = SavingsAccount::with('customers')
            ->where('status', 'ACTIVE')
            ->get()
            ->filter(fn($a) => $a->customers && $a->customers->count() > 0)
            ->map(function ($a) {
                $customerName = $a->customers->first()
                    ? $a->customers->first()->first_name . ' ' . $a->customers->first()->last_name
                    : 'No Customer';

                return [
                    'name' => $a->account_number . ' - ' . $customerName,
                    'id' => $a->id,
                ];
            })
            ->toArray();
    }

    protected function generateFdNumber($accountNumber)
    {
        return str_replace('SA', 'FD', $accountNumber);
    }

    public function updatedLinkedAccountId($value)
    {
        if ($value) {
            $linkedAccount = SavingsAccount::with('customers')->find($value);

            if ($linkedAccount) {
                // Log the account number for debugging
                logger()->info('Generating FD number from account:', [
                    'savings_number' => $linkedAccount->account_number,
                    'fd_number' => str_replace('SA', 'FD', $linkedAccount->account_number)
                ]);

                // Auto-generate FD number from linked savings account
                $this->fd_number = $this->generateFdNumber($linkedAccount->account_number);

                // Auto-fill customer_id of the first linked customer
                if ($linkedAccount->customers && $linkedAccount->customers->first()) {
                    $this->customer_id = $linkedAccount->customers->first()->id;
                }

                // Auto-fill branch_id from the linked savings account
                if ($linkedAccount->branch_id) {
                    $this->branch_id = $linkedAccount->branch_id;
                }
            }
        }
    }

    // Explicit handler called from the Blade select change event to ensure immediate update
    public function onLinkedAccountChange($value)
    {
        if (! $value) {
            return;
        }

        $linkedAccount = SavingsAccount::with('customers')->find($value);
        if (! $linkedAccount) {
            logger()->warning('onLinkedAccountChange: linked account not found', ['id' => $value]);
            return;
        }

        // Generate FD number by replacing SA with FD (preserve rest of the number)
        $savingsNumber = $linkedAccount->account_number;
        $fd = str_replace('SA', 'FD', $savingsNumber);
        logger()->info('onLinkedAccountChange generated FD', ['savings' => $savingsNumber, 'fd' => $fd]);

        $this->fd_number = $fd;

        // Also set customer_id if available
        if ($linkedAccount->customers && $linkedAccount->customers->first()) {
            $this->customer_id = $linkedAccount->customers->first()->id;
        }

        // Also set branch_id from the linked savings account
        if ($linkedAccount->branch_id) {
            $this->branch_id = $linkedAccount->branch_id;
        }
    }

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'fd_type_id' => 'required|exists:fixed_deposit_type,id',
        'linked_account_id' => 'required|exists:savings_account,id',
        'branch_id' => 'required|exists:branch,id',
        'principal_amount' => 'required|numeric|min:0',
        'interest_freq' => 'required|in:MONTHLY,END',
        'interest_payout_option' => 'required|in:TRANSFER_TO_SAVINGS,RENEW_FD',
        'auto_renewal' => 'required|boolean',
    ];

    public function submit()
    {
        $this->validate();

        // Get the FD type to calculate maturity date
        $fdType = \App\Models\FixedDepositType::find($this->fd_type_id);
        
        $startDate = now();
        $maturityDate = now()->addMonths($fdType->tenure_months);
        
        // Calculate maturity amount (simple calculation - you may need to adjust based on your business logic)
        $interestAmount = ($this->principal_amount * $fdType->interest_rate * $fdType->tenure_months) / (12 * 100);
        $maturityAmount = $this->principal_amount + $interestAmount;

        FixedDeposit::create([
            'fd_number' => $this->fd_number,
            'customer_id' => $this->customer_id,
            'fd_type_id' => $this->fd_type_id,
            'branch_id' => $this->branch_id,
            'linked_account_id' => $this->linked_account_id,
            'principal_amount' => $this->principal_amount,
            'interest_freq' => $this->interest_freq,
            'maturity_amount' => $maturityAmount,
            'start_date' => $startDate,
            'maturity_date' => $maturityDate,
            'status' => 'ACTIVE',
            'interest_payout_option' => $this->interest_payout_option,
            'auto_renewal' => $this->auto_renewal,
        ]);

        session()->flash('success', 'Fixed Deposit created successfully!');
        $this->reset();
    }
};
?>




<x-mary-form wire:submit.prevent="submit">
    @if (session()->has('success'))
        <x-mary-alert type="success" class="mb-4">
            {{ session('success') }}
        </x-mary-alert>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">

        <!-- Linked Account ID (Savings Account) -->
        <x-mary-select
            label="Linked Account (select savings account)"
            wire:model="linked_account_id"
            wire:change="onLinkedAccountChange($event.target.value)"
            :options="$linkedAccountOptions"
            required
            placeholder="Select account"
            class="text-sm w-[300px] focus:ring-0 focus:outline-none"
        />

        <!-- FD Number  -->
        <x-mary-input label="FD Number" wire:model="fd_number" readonly class="text-base w-[300px] focus:ring-0 focus:outline-none" />

        <!-- Customer ID (Hidden, auto-filled from linked account) -->
        <input type="hidden" wire:model="customer_id" />

        <!-- Branch -->
        <x-mary-select 
            label="Branch"
            wire:model="branch_id"
            :options="$branchOptions"
            required 
            placeholder="Select branch"
            class="text-sm w-[300px] focus:ring-0 focus:outline-none"
        />

        <!-- FD Type -->
        <x-mary-select 
            label="FD Type"
            wire:model="fd_type_id"
            :options="$fdTypeOptions"
            required 
            placeholder="Select FD Type"
            class="text-sm w-[300px] focus:ring-0 focus:outline-none"
        />

        <!-- Principal Amount -->
        <x-mary-input 
            label="Principal Amount" 
            wire:model="principal_amount" 
            type="number" 
            step="0.01"
            required 
            class="text-sm w-[300px] focus:ring-0 focus:outline-none" 
        />

        <!-- Interest Frequency -->
        <x-mary-select 
            label="Interest Frequency" 
            wire:model="interest_freq" 
            :options="$interestFrequencyOptions"
            required 
            placeholder="Select frequency"
            class="text-sm w-[300px] focus:ring-0 focus:outline-none" 
        />

        <!-- Interest Payout Option -->
        <x-mary-select 
            label="Interest Payout Option" 
            wire:model="interest_payout_option" 
            :options="$interestPayoutOptions"
            required 
            placeholder="Select interest payout option"
            class="text-sm w-[300px] focus:ring-0 focus:outline-none" 
        />

        <!-- Auto Renewal -->
        <x-mary-select 
            label="Auto Renewal" 
            wire:model="auto_renewal" 
            :options="$autoRenewalOptions"
            required 
            placeholder="Select auto renewal option"
            class="text-sm w-[300px] focus:ring-0 focus:outline-none" 
        />
    </div>

    <!-- Button -->
    <x-slot:actions>
        <div class="flex justify-end mt-8 w-full">
            <x-mary-button 
                label="Create Fixed Deposit"  
                class="btn-primary"
                type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>
