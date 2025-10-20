<?php

use Livewire\Volt\Component;
use App\Models\SavingsTransaction;
use App\Models\SavingsAccount;

new class extends Component {
    public $full_name;
    public $email;
    public $phone;
    public $nic_number;
    public $account_number;
    public $amount;
    public $description;
    public $account_search = '';
    public $account_balance = null;
    public $account_type = null;

    public $accounts = [];
    public $filteredAccounts = [];

    public $showTransactionModal = false;
    public $transactionMessage = '';
    public $generalError = '';
    public $generalSuccess = '';

    protected $rules = [
        'account_number' => 'nullable|exists:savings_account,account_number',
        'amount' => 'required|numeric|min:1',
        'description' => 'nullable|string|max:255',
        'full_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'nic_number' => 'nullable|string|max:20',
    ];

    public function mount()
    {
        $this->accounts = SavingsAccount::with(['customers', 'accountType'])->get();
        $this->filteredAccounts = $this->accounts->take(10);
    }

    public function updatedAccountSearch($value)
    {
        if (strlen($value) > 0) {
            $this->filteredAccounts = SavingsAccount::with(['customers', 'accountType'])
                ->where('account_number', 'like', '%' . $value . '%')
                ->orWhereHas('customers', function($query) use ($value) {
                    $query->where('first_name', 'like', '%' . $value . '%')
                          ->orWhere('last_name', 'like', '%' . $value . '%');
                })
                ->limit(10)
                ->get();
        } else {
            $this->filteredAccounts = $this->accounts->take(10);
        }
    }

    public function searchAccount()
    {
        $this->generalError = '';
        $this->generalSuccess = '';

        if (!$this->account_number) {
            $this->generalError = 'Please enter an account number.';
            return;
        }

        $account = SavingsAccount::with(['customers', 'accountType'])
            ->where('account_number', $this->account_number)
            ->first();

        if ($account) {
            $customer = $account->customers->first();

            if ($customer) {
                $this->full_name = $customer->first_name . ' ' . $customer->last_name;
                $this->email = $customer->email ?? '';
                $this->phone = $customer->phone ?? '';
                $this->nic_number = $customer->id_number ?? '';
            }

            $this->account_balance = $account->balance;
            $this->account_type = $account->accountType->name ?? '';

            $this->generalSuccess = 'Account details loaded successfully.';
        } else {
            $this->generalError = 'Account not found.';
            $this->reset(['full_name', 'email', 'phone', 'nic_number', 'account_balance', 'account_type']);
        }
    }

    public function submit()
    {
        $this->generalError = '';
        $this->generalSuccess = '';

        $this->validate();

        $fromAccount = SavingsAccount::where('account_number', $this->account_number)->first();

        // Call DB function to check withdraw ability
        $canWithdraw = false;
        try {
            $result = \DB::select("SELECT can_withdraw(?, ?) as allowed", [$this->account_number, $this->amount]);
            $canWithdraw = $result[0]->allowed ?? false;
        } catch (\Exception $e) {
            $this->generalError = 'Withdrawal check failed: ' . $e->getMessage();
            return;
        }

        if (!$canWithdraw) {
            $this->generalError = 'Withdrawal not allowed: Insufficient balance, below minimum, or withdrawal limit reached.';
            return;
        }

        $balanceBefore = $fromAccount->balance;
        $balanceAfter = $balanceBefore - $this->amount;

        $fromAccount->balance = $balanceAfter;
        $fromAccount->last_transaction_date = now();
        $fromAccount->save();

        SavingsTransaction::create([
            'type' => 'WITHDRAWAL',
            'from_id' => $fromAccount->id,
            'to_id' => null,
            'amount' => $this->amount,
            'status' => 'COMPLETED',
            'description' => $this->description,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ]);

        $this->transactionMessage = 'Withdrawal of Rs. ' . number_format($this->amount, 2) . ' completed successfully! New balance: Rs. ' . number_format($balanceAfter, 2);
        $this->showTransactionModal = true;

        $this->reset(['full_name', 'email', 'phone', 'nic_number', 'account_number', 'amount', 'description', 'account_balance', 'account_type']);
        $this->mount();
    }
};
?>

<div>
    {{-- Transaction Success Modal --}}
    <x-mary-modal wire:model="showTransactionModal" title="Transaction Successful!" box-class="border-2 border-success">
        <div class="flex flex-col items-center gap-4">
            <x-mary-icon name="o-check-circle" class="w-16 h-16 text-success" />
            <p class="text-center text-lg">{{ $transactionMessage }}</p>
        </div>
        <x-slot:actions>
            <x-mary-button label="Close" wire:click="showTransactionModal = false" class="btn-success" />
        </x-slot:actions>
    </x-mary-modal>

    <x-mary-form wire:submit.prevent="submit">
        {{-- General Alerts --}}
        @if($generalError)
            <x-mary-alert title="Error" description="{{ $generalError }}" icon="o-exclamation-triangle" class="alert-error mb-4" dismissible />
        @endif

        @if($generalSuccess)
            <x-mary-alert title="Success" description="{{ $generalSuccess }}" icon="o-check-circle" class="alert-success mb-4" dismissible />
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Account Number with Search -->
        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium mb-2">Account Number *</label>
            <div class="flex gap-2">
                <div class="flex-1">
                    <x-mary-input
                        wire:model.live.debounce.300ms="account_search"
                        wire:model="account_number"
                        placeholder="Search by account number or customer name"
                        list="accounts-list"
                        required
                        class="w-full" />
                    <datalist id="accounts-list">
                        @foreach($filteredAccounts as $acc)
                            <option value="{{ $acc->account_number }}">
                                {{ $acc->account_number }} - {{ $acc->customers->first()->first_name ?? 'N/A' }} {{ $acc->customers->first()->last_name ?? '' }}
                            </option>
                        @endforeach
                    </datalist>
                </div>
                <x-mary-button
                    wire:click="searchAccount"
                    icon="o-magnifying-glass"
                    class="btn-primary"
                    type="button"
                    tooltip="Search Account" />
            </div>
        </div>

        <!-- Account Info Display -->
        @if($account_balance !== null || $account_type)
            <div class="col-span-1 md:col-span-2 bg-base-200 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($account_type)
                        <div>
                            <span class="text-sm font-semibold text-base-content/70">Account Type:</span>
                            <span class="badge badge-info badge-soft ml-2">{{ $account_type }}</span>
                        </div>
                    @endif
                    @if($account_balance !== null)
                        <div>
                            <span class="text-sm font-semibold text-base-content/70">Current Balance:</span>
                            <span class="font-semibold text-success ml-2">Rs. {{ number_format($account_balance, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <x-mary-input label="Full Name" wire:model="full_name" required class="w-full" readonly />
        <x-mary-input label="Email" wire:model="email" type="email" required class="w-full" readonly />
        <x-mary-input label="Phone Number" wire:model="phone" required class="w-full" readonly />
        <x-mary-input label="NIC Number" wire:model="nic_number" required class="w-full" readonly />
        <x-mary-input label="Amount (Rs.)" wire:model="amount" type="number" min="0" step="0.01" required class="w-full" />
        <div class="col-span-1 md:col-span-2">
            <x-mary-textarea label="Description" wire:model="description" rows="2" class="w-full" />
        </div>

    </div>

        <!-- Submit Button -->
        <x-slot:actions>
            <div class="flex justify-center mt-6 w-full">
                <x-mary-button label="Withdraw Money" class="w-100 btn-primary" type="submit" />
            </div>
        </x-slot:actions>
    </x-mary-form>
</div>
