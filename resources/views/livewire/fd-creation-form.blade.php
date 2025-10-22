<?php

use App\Models\SavingsAccount;
use Livewire\Volt\Component;
use App\Models\FixedDeposit;

new class extends Component {
    public $fd_number;
    public $nic_number;
    public $fd_type;
    public $linked_account_id;
    public $branch_id;
    public $interest_frequency = null;
    public $maturity_number;
    public $interest_payout = null;
    public $auto_renewal = null;

    public $fdTypeOptions = [
        ['label' => 'Fixed', 'value' => 'fixed'],
        ['label' => 'Recurring', 'value' => 'recurring'],
    ];

    public $interestFrequencyOptions = [
        ['label' => 'Monthly', 'value' => 'MONTHLY'],
        ['label' => 'End', 'value' => 'END'],
    ];

    public $interestPayoutOptions = [
        ['label' => 'Reinvest', 'value' => 'Reinvest'],
        ['label' => 'Payout', 'value' => 'Payout'],
    ];

    public $autoRenewalOptions = [
        ['label' => 'Yes', 'value' => true],
        ['label' => 'No', 'value' => false],
    ];

    // Linked savings account dropdown
    public $linkedAccountOptions = [];

    public function mount()
    {
        $this->linkedAccountOptions = SavingsAccount::with('customers')
            ->where('status', 'ACTIVE')
            ->get()
            ->filter(fn($a) => $a->customers && $a->customers->count() > 0)
            ->map(function ($a) {
                $customerName = $a->customers->first()
                    ? $a->customers->first()->first_name . ' ' . $a->customers->first()->last_name
                    : 'No Customer';

                return [
                    'label' => $a->account_number . ' - ' . $customerName,
                    'value' => $a->id,
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

                // Auto-fill NIC number of the first linked customer
                if ($linkedAccount->customers && $linkedAccount->customers->first()) {
                    $this->nic_number = $linkedAccount->customers->first()->id_number;
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

        // Also set NIC if available
        if ($linkedAccount->customers && $linkedAccount->customers->first()) {
            $this->nic_number = $linkedAccount->customers->first()->id_number;
        }
    }

    protected $rules = [
        'nic_number' => 'required|string|max:20|exists:customers,id_number',
        'fd_type' => 'required|in:fixed,recurring',
        'linked_account_id' => 'required|exists:savings_accounts,id',
        'interest_frequency' => 'required|in:MONTHLY,END',
        'maturity_number' => 'required|integer|min:1',
        'interest_payout' => 'required|in:Reinvest,Payout',
        'auto_renewal' => 'required|boolean',
    ];

    public function submit()
    {
        $this->validate();

        FixedDeposit::create([
            'fd_number' => $this->fd_number,
            'nic_number' => $this->nic_number,
            'fd_type_id' => $this->fd_type,
            'linked_account_id' => $this->linked_account_id,
            'interest_frequency' => $this->interest_frequency,
            'maturity_number' => $this->maturity_number,
            'interest_payout' => $this->interest_payout,
            'auto_renewal' => $this->auto_renewal,
        ]);

        $this->reset();
    }
};
?>




<x-mary-form wire:submit.prevent="submit">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-base w-full">

        <!-- Linked Account ID (Savings Account) -->
        <x-mary-select
            label="Linked Account (select savings account)"
            wire:model="linked_account_id"
            wire:change="onLinkedAccountChange($event.target.value)"
            :options="$linkedAccountOptions"
            option-label="label"
            option-value="value"
            required
            class="text-sm w-[300px] focus:ring-0 focus:outline-none"
        />

        <!-- FD Number  -->
        <x-mary-input label="FD Number" wire:model="fd_number" readonly class="text-base w-[300px] focus:ring-0 focus:outline-none" />

        <!-- NIC Number  -->
        <x-mary-input label="NIC Number" wire:model="nic_number" required class="text-sm w-[300px] focus:ring-0 focus:outline-none" readonly />

        <!-- FD Type -->
        <x-mary-select 
            label="FD Type"
            wire:model="fd_type"
            :options="$fdTypeOptions"
            option-label="label"
            option-value="value"
            required class="text-sm w-[300px] focus:ring-0 focus:outline-none"/>



        

        <!-- Interest Frequency in Months  -->
        <x-mary-select label="Interest Frequency (Months)" wire:model="interest_frequency" 
            :options="$interestFrequencyOptions"
            option-value="value"
            option-label="label"
            required class="text-sm w-[300px] focus:ring-0 focus:outline-none" />

        <!-- Maturity Number  -->
        <x-mary-input label="Maturity Number (Months)" wire:model="maturity_number" class="text-sm w-[300px] focus:ring-0 focus:outline-none"
 />

        <!-- Interest Payout  -->
        <x-mary-select label="Interest Payout" wire:model="interest_payout" 
            :options="$interestPayoutOptions"
            option-value="value"
            option-label="label"
            required class="text-sm w-[300px] focus:ring-0 focus:outline-none" />

        <!-- Auto Renewal -->
        <x-mary-select label="Auto Renewal" wire:model="auto_renewal" 
            :options="$autoRenewalOptions"
            option-value="value"
            option-label="label"
            required class="text-sm w-[300px] focus:ring-0 focus:outline-none" />
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
