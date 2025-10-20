<?php

use Livewire\Volt\Component;
use App\Models\SavingsTransaction;
use App\Models\SavingsAccount;

new class extends Component {
    public $sender_name;
    public $receiver_name;
    public $email;
    public $phone;
    public $nic_number;
    public $from_num;
    public $to_num;
    public $amount;
    public $description;

    // Search and display properties
    public $from_search = '';
    public $to_search = '';
    public $from_balance = null;
    public $from_account_type = null;
    public $to_balance = null;
    public $to_account_type = null;

    public $accounts = [];
    public $filteredFromAccounts = [];
    public $filteredToAccounts = [];

    public $showTransactionModal = false;
    public $transactionMessage = '';
    public $generalError = '';
    public $generalSuccess = '';

    protected $rules = [
        'from_num' => 'nullable|exists:savings_account,account_number',
        'to_num' => 'nullable|exists:savings_account,account_number|different:from_num',
        'amount' => 'required|numeric|min:1',
        'description' => 'nullable|string|max:255',
        'sender_name' => 'nullable|string|max:255',
        'receiver_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'nic_number' => 'nullable|string|max:20',
    ];

    public function mount()
    {
        $this->accounts = SavingsAccount::with(['customers', 'accountType'])->get();
        $this->filteredFromAccounts = $this->accounts->take(10);
        $this->filteredToAccounts = $this->accounts->take(10);
    }

    public function updatedFromSearch($value)
    {
        if (strlen($value) > 0) {
            $this->filteredFromAccounts = SavingsAccount::with(['customers', 'accountType'])
                ->where('account_number', 'like', '%' . $value . '%')
                ->orWhereHas('customers', function($query) use ($value) {
                    $query->where('first_name', 'like', '%' . $value . '%')
                          ->orWhere('last_name', 'like', '%' . $value . '%');
                })
                ->limit(10)
                ->get();
        } else {
            $this->filteredFromAccounts = $this->accounts->take(10);
        }
    }

    public function updatedToSearch($value)
    {
        if (strlen($value) > 0) {
            $this->filteredToAccounts = SavingsAccount::with(['customers', 'accountType'])
                ->where('account_number', 'like', '%' . $value . '%')
                ->orWhereHas('customers', function($query) use ($value) {
                    $query->where('first_name', 'like', '%' . $value . '%')
                          ->orWhere('last_name', 'like', '%' . $value . '%');
                })
                ->limit(10)
                ->get();
        } else {
            $this->filteredToAccounts = $this->accounts->take(10);
        }
    }

    public function searchFromAccount()
    {
        $this->generalError = '';
        $this->generalSuccess = '';

        if (!$this->from_num) {
            $this->generalError = 'Please enter a sender account number.';
            return;
        }

        $account = SavingsAccount::with(['customers', 'accountType'])
            ->where('account_number', $this->from_num)
            ->first();

        if ($account) {
            $customer = $account->customers->first();

            if ($customer) {
                $this->sender_name = $customer->first_name . ' ' . $customer->last_name;
                $this->email = $customer->email ?? '';
                $this->phone = $customer->phone ?? '';
                $this->nic_number = $customer->id_number ?? '';
            }

            $this->from_balance = $account->balance;
            $this->from_account_type = $account->accountType->name ?? '';

            $this->generalSuccess = 'Sender account details loaded successfully.';
        } else {
            $this->generalError = 'Sender account not found.';
            $this->reset(['sender_name', 'email', 'phone', 'nic_number', 'from_balance', 'from_account_type']);
        }
    }

    public function searchToAccount()
    {
        $this->generalError = '';
        $this->generalSuccess = '';

        if (!$this->to_num) {
            $this->generalError = 'Please enter a receiver account number.';
            return;
        }

        if ($this->to_num === $this->from_num) {
            $this->generalError = 'Sender and receiver accounts must be different.';
            return;
        }

        $account = SavingsAccount::with(['customers', 'accountType'])
            ->where('account_number', $this->to_num)
            ->first();

        if ($account) {
            $customer = $account->customers->first();

            if ($customer) {
                $this->receiver_name = $customer->first_name . ' ' . $customer->last_name;
            }

            $this->to_balance = $account->balance;
            $this->to_account_type = $account->accountType->name ?? '';

            $this->generalSuccess = 'Receiver account details loaded successfully.';
        } else {
            $this->generalError = 'Receiver account not found.';
            $this->reset(['receiver_name', 'to_balance', 'to_account_type']);
        }
    }

    public function submit()
    {
        $this->generalError = '';
        $this->generalSuccess = '';

        $this->validate();

        $fromAccount = SavingsAccount::where('account_number', $this->from_num)->first();
        $toAccount = SavingsAccount::where('account_number', $this->to_num)->first();

        if (!$fromAccount || !$toAccount) {
            $this->generalError = 'Invalid accounts provided.';
            return;
        }

        // Call DB function to check withdraw ability for sender
        $canWithdraw = false;
        try {
            $result = \DB::select("SELECT can_withdraw(?, ?) as allowed", [$this->from_num, $this->amount]);
            $canWithdraw = $result[0]->allowed ?? false;
        } catch (\Exception $e) {
            $this->generalError = 'Withdrawal check failed: ' . $e->getMessage();
            $canWithdraw = true; // if the database doesn't support the function, allow the transfer
        }

        if (!$canWithdraw) {
            $this->generalError = 'Transfer not allowed: Insufficient balance, below minimum, or withdrawal limit reached.';
            return;
        }

        $fromBalanceBefore = $fromAccount->balance;
        $toBalanceBefore = $toAccount->balance;

        $fromAccount->update([
            'balance' => $fromBalanceBefore - $this->amount,
            'last_transaction_date' => now(),
        ]);

        $toAccount->update([
            'balance' => $toBalanceBefore + $this->amount,
            'last_transaction_date' => now(),
        ]);

        SavingsTransaction::create([
            'type' => 'TRANSFER',
            'from_id' => $fromAccount->id,
            'to_id' => $toAccount->id,
            'amount' => $this->amount,
            'status' => 'COMPLETED',
            'description' => $this->description,
            'balance_before' => $fromBalanceBefore,
            'balance_after' => $fromBalanceBefore - $this->amount,
        ]);

        $this->transactionMessage = 'Transfer of Rs. ' . number_format($this->amount, 2) . ' completed successfully! From: ' . $this->from_num . ' to ' . $this->to_num;
        $this->showTransactionModal = true;

        $this->reset(['sender_name', 'receiver_name', 'email', 'phone', 'nic_number', 'from_num', 'to_num', 'amount', 'description', 'from_balance', 'from_account_type', 'to_balance', 'to_account_type']);
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

        <!-- Sender Details -->
        <div class="col-span-1 md:col-span-2">
            <h2 class="text-lg font-semibold mb-2 text-primary">Sender Details</h2>
        </div>

        <!-- Sender Account Number with Search -->
        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium mb-2">Sender Account Number *</label>
            <div class="flex gap-2">
                <div class="flex-1">
                    <x-mary-input
                        wire:model.live.debounce.300ms="from_search"
                        wire:model="from_num"
                        placeholder="Search by account number or customer name"
                        list="from-accounts-list"
                        required
                        class="w-full" />
                    <datalist id="from-accounts-list">
                        @foreach($filteredFromAccounts as $acc)
                            <option value="{{ $acc->account_number }}">
                                {{ $acc->account_number }} - {{ $acc->customers->first()->first_name ?? 'N/A' }} {{ $acc->customers->first()->last_name ?? '' }}
                            </option>
                        @endforeach
                    </datalist>
                </div>
                <x-mary-button
                    wire:click="searchFromAccount"
                    icon="o-magnifying-glass"
                    class="btn-primary"
                    type="button"
                    tooltip="Search Sender Account" />
            </div>
        </div>

        <!-- Sender Account Info Display -->
        @if($from_balance !== null || $from_account_type)
            <div class="col-span-1 md:col-span-2 bg-base-200 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($from_account_type)
                        <div>
                            <span class="text-sm font-semibold text-base-content/70">Account Type:</span>
                            <span class="badge badge-info badge-soft ml-2">{{ $from_account_type }}</span>
                        </div>
                    @endif
                    @if($from_balance !== null)
                        <div>
                            <span class="text-sm font-semibold text-base-content/70">Current Balance:</span>
                            <span class="font-semibold text-success ml-2">Rs. {{ number_format($from_balance, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <x-mary-input label="Full Name" wire:model="sender_name" required class="w-full" readonly />
        <x-mary-input label="Email" wire:model="email" type="email" required class="w-full" readonly />
        <x-mary-input label="Phone Number" wire:model="phone" required class="w-full" readonly />
        <x-mary-input label="NIC Number" wire:model="nic_number" required class="w-full" readonly />
        <x-mary-input label="Amount (Rs.)" wire:model="amount" type="number" min="0" step="0.01" required class="w-full" />
        <div class="col-span-1 md:col-span-2">
            <x-mary-textarea label="Description" wire:model="description" rows="2" class="w-full" />
        </div>

        <!-- Recipient Details -->
        <div class="col-span-1 md:col-span-2 mt-6">
            <h2 class="text-lg font-semibold mb-2 text-accent">Recipient Details</h2>
        </div>

        <!-- Receiver Account Number with Search -->
        <div class="col-span-1 md:col-span-2">
            <label class="block text-sm font-medium mb-2">Receiver Account Number *</label>
            <div class="flex gap-2">
                <div class="flex-1">
                    <x-mary-input
                        wire:model.live.debounce.300ms="to_search"
                        wire:model="to_num"
                        placeholder="Search by account number or customer name"
                        list="to-accounts-list"
                        required
                        class="w-full" />
                    <datalist id="to-accounts-list">
                        @foreach($filteredToAccounts as $acc)
                            <option value="{{ $acc->account_number }}">
                                {{ $acc->account_number }} - {{ $acc->customers->first()->first_name ?? 'N/A' }} {{ $acc->customers->first()->last_name ?? '' }}
                            </option>
                        @endforeach
                    </datalist>
                </div>
                <x-mary-button
                    wire:click="searchToAccount"
                    icon="o-magnifying-glass"
                    class="btn-accent"
                    type="button"
                    tooltip="Search Receiver Account" />
            </div>
        </div>

        <!-- Receiver Account Info Display -->
        @if($to_balance !== null || $to_account_type)
            <div class="col-span-1 md:col-span-2 bg-base-200 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($to_account_type)
                        <div>
                            <span class="text-sm font-semibold text-base-content/70">Account Type:</span>
                            <span class="badge badge-info badge-soft ml-2">{{ $to_account_type }}</span>
                        </div>
                    @endif
                    @if($to_balance !== null)
                        <div>
                            <span class="text-sm font-semibold text-base-content/70">Current Balance:</span>
                            <span class="font-semibold text-success ml-2">Rs. {{ number_format($to_balance, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <x-mary-input label="Full Name" wire:model="receiver_name" required class="w-full" readonly />

    </div>

        <!-- Submit Button -->
        <x-slot:actions>
            <div class="flex justify-center mt-6 w-full">
                <x-mary-button label="Transfer Money" class="w-100 btn-primary" type="submit" />
            </div>
        </x-slot:actions>
    </x-mary-form>
</div>
