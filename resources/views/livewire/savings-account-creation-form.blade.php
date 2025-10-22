<?php

use Livewire\Volt\Component;
use App\Models\SavingsAccount;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\SavingsAccountType;
use App\Models\CustomerStatusType;
use App\Enums\AccountStatusEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

new class extends Component {
    public $branches = [];
    public $branch_id = '';
    public $account_number = '';
    public $account_type_id = '';
    public $account_type_name = '';
    public $balance = 0;
    public $status = '';
    public $opened_date = '';

    public $nic_input = '';
    public $customer_list = []; // NIC + Name + Age
    public $customer_search = '';
    public $filteredCustomers = [];

    protected $rules = [
        // Migration creates table `branch` (singular) so validate against that
        'branch_id' => 'required|integer|exists:branch,id',
        // account_type must exist in savings_account_type
        'account_type_id' => 'required|integer|exists:savings_account_type,id',
        'balance' => 'required|numeric|min:0',
        'status' => 'required|in:ACTIVE,INACTIVE',
    ];

    public function mount()
    {
        // Load branches for dropdown
        $this->branches = Branch::all()
            ->map(fn($branch) => ['id' => $branch->id, 'name' => $branch->branch_name])
            ->toArray();

        $this->status = AccountStatusEnum::ACTIVE->value;
        $this->opened_date = now()->format('Y-m-d');

        // Optional: set first branch as default
        $this->branch_id = $this->branches[0]['id'] ?? null;

        // Load initial customers for datalist
        $this->filteredCustomers = Customer::limit(10)->get();
    }

    public function updatedCustomerSearch($value)
    {
        if (strlen($value) > 0) {
            $this->filteredCustomers = Customer::where('id_number', 'like', '%' . $value . '%')
                ->orWhere('first_name', 'like', '%' . $value . '%')
                ->orWhere('last_name', 'like', '%' . $value . '%')
                ->limit(10)
                ->get();
        } else {
            $this->filteredCustomers = Customer::limit(10)->get();
        }
    }

    public function addNic()
    {
        $this->validate(['nic_input' => 'required|string']);

        // Prevent duplicate NIC
        if (in_array($this->nic_input, array_column($this->customer_list, 'nic'))) {
            $this->addError('nic_input', 'This NIC has already been added.');
            return;
        }

        $customer = Customer::where('id_number', $this->nic_input)->first();
        if (!$customer) {
            $this->addError('nic_input', 'Customer not found.');
            return;
        }

        $age = Carbon::parse($customer->date_of_birth)->age;

        // Find customer status dynamically from DB
        $customerStatus = CustomerStatusType::where('min_age', '<=', $age)
            ->where(function($q) use ($age) {
                $q->where('max_age', '>=', $age)
                  ->orWhereNull('max_age'); // For seniors
            })
            ->first();

        if (!$customerStatus) {
            $this->addError('nic_input', 'Customer status not found for this age.');
            return;
        }

        // Find savings account type for this customer status
        $accountType = SavingsAccountType::where('customer_status_id', $customerStatus->id)->first();
        if (!$accountType) {
            $this->addError('nic_input', 'Account type not found in DB.');
            return;
        }

        // Add customer to list
        $this->customer_list[] = [
            'nic' => $customer->id_number,
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'age' => $age,
            'account_type_name' => $accountType->name,
            'account_type_id' => $accountType->id,
        ];

        // Auto-fill Account Type: Joint if more than one customer, otherwise first customer's type
        if (count($this->customer_list) > 1) {
            $jointAccountType = SavingsAccountType::where('name', 'Joint')->first();
            if ($jointAccountType) {
                $this->account_type_name = $jointAccountType->name;
                $this->account_type_id = $jointAccountType->id;
            }
        } else {
            $firstCustomer = $this->customer_list[0];
            $this->account_type_name = $firstCustomer['account_type_name'];
            $this->account_type_id = $firstCustomer['account_type_id'];
        }

        $this->reset('nic_input');
    }

    public function removeNic($index)
    {
        unset($this->customer_list[$index]);
        $this->customer_list = array_values($this->customer_list);

        if (!empty($this->customer_list)) {
            // If more than one customer remains, use Joint account type
            if (count($this->customer_list) > 1) {
                $jointAccountType = SavingsAccountType::where('name', 'Joint')->first();
                if ($jointAccountType) {
                    $this->account_type_name = $jointAccountType->name;
                    $this->account_type_id = $jointAccountType->id;
                }
            } else {
                // Only one customer left, use their account type
                $firstCustomer = $this->customer_list[0];
                $this->account_type_name = $firstCustomer['account_type_name'];
                $this->account_type_id = $firstCustomer['account_type_id'];
            }
        } else {
            $this->account_type_name = '';
            $this->account_type_id = '';
        }
    }

    // Generate unique account number like SA4847010164
    protected function generateAccountNumber()
    {
        $prefix = 'SA';
        $branchCode = str_pad($this->branch_id ?? rand(1, 9999), 4, '0', STR_PAD_LEFT);

        do {
            $randomDigits = rand(100000, 999999);
            $accountNumber = $prefix . $branchCode . $randomDigits;
            // Use direct DB table check to avoid relying on model pluralization cache during runtime
        } while (DB::table('savings_account')->where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

    public function submit()
    {
        if (empty($this->customer_list)) {
            $this->addError('customer_list', 'Please add at least one customer.');
            return;
        }

        // Validate form inputs before attempting DB operations
        $this->validate();

        // Check if initial deposit meets minimum balance requirement
        $accountType = SavingsAccountType::find($this->account_type_id);
        if ($accountType && $this->balance < $accountType->min_balance) {
            $this->addError('balance', 'Initial deposit must be at least Rs. ' . number_format($accountType->min_balance, 2) . ' for ' . $accountType->name . ' account type.');
            return;
        }

        // Generate account number
        $this->account_number = $this->generateAccountNumber();

        try {
            // Log the validated payload for debugging (will appear in storage/logs/laravel.log)
            $validated = $this->validate();
            logger()->info('SavingsAccount submit called', [
                'validated' => $validated,
                'customer_list' => $this->customer_list,
                'branch_id_type' => gettype($this->branch_id),
                'account_type_id_type' => gettype($this->account_type_id),
            ]);

            DB::transaction(function () {
                // Recompute customer ids inside transaction
                $customer_ids = Customer::whereIn('id_number', array_column($this->customer_list, 'nic'))
                    ->pluck('id')
                    ->toArray();

                logger()->info('Customer IDs to attach', ['customer_ids' => $customer_ids]);

                $account = SavingsAccount::create([
                    'account_number' => $this->account_number,
                    'account_type_id' => $this->account_type_id,
                    'branch_id' => $this->branch_id,
                    'balance' => $this->balance,
                    'status' => $this->status,
                    'opened_date' => $this->opened_date,
                ]);

                logger()->info('SavingsAccount created', ['id' => $account->id ?? null, 'account_number' => $account->account_number ?? null]);

                if (!empty($customer_ids)) {
                    $account->customers()->attach($customer_ids);
                    logger()->info('Attached customers to account', ['account_id' => $account->id, 'attached' => $customer_ids]);
                } else {
                    logger()->warning('No customer IDs found to attach', ['account_id' => $account->id, 'customer_list' => $this->customer_list]);
                }
            });
        } catch (\Throwable $e) {
            // Log the exception and add a user-visible error
            logger()->error('Failed to create savings account: ' . $e->getMessage(), ['exception' => $e]);
            $this->addError('submit', 'Failed to create savings account. Check logs for details.');
            return;
        }

        $this->dispatch('toast', title: 'Savings account created successfully.');
        session()->flash('success', 'Savings account created successfully! Account Number: ' . $this->account_number);

        $this->reset([
            'account_number', 'account_type_id', 'account_type_name',
            'branch_id', 'balance', 'status', 'opened_date',
            'customer_list', 'nic_input'
        ]);
    }
};
?>

<x-mary-form wire:submit.prevent="submit">
    @if (session()->has('success'))
        <x-mary-alert type="success" class="mb-4">
            {{ session('success') }}
        </x-mary-alert>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- NIC Input -->
        <div class="md:col-span-2">
            <label class="label">
                <span class="label-text">Customer NICs</span>
            </label>

            <div class="flex items-center gap-2">
                <x-mary-input 
                    placeholder="Search by NIC, first name, or last name" 
                    wire:model.live.debounce.300ms="customer_search"
                    wire:model="nic_input" 
                    wire:keydown.enter.prevent="addNic" 
                    list="customers-list"
                    class="flex-grow" />
                <datalist id="customers-list">
                    @foreach($filteredCustomers as $customer)
                        <option value="{{ $customer->id_number }}">
                            {{ $customer->id_number }} - {{ $customer->first_name }} {{ $customer->last_name }}
                        </option>
                    @endforeach
                </datalist>
                <x-mary-button label="Add" wire:click.prevent="addNic" class="btn-primary" />
            </div>
            @error('nic_input') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

            <div class="mt-4 space-y-2">
                @foreach ($customer_list as $index => $cust)
                    <x-mary-list-item :item="['name' => $cust['nic'].' - '.$cust['name']]" no-separator>
                        <x-slot:actions>
                            <x-mary-button icon="o-trash" wire:click.prevent="removeNic({{ $index }})" class="btn-sm btn-ghost text-red-500" />
                        </x-slot:actions>
                    </x-mary-list-item>
                @endforeach
            </div>
        </div>

        <!-- Account Type -->
        <div>
            <x-mary-input 
                label="Account Type" 
                wire:model="account_type_name" 
                readonly 
                required 
            />
            @if($account_type_id)
                @php
                    $accType = \App\Models\SavingsAccountType::find($account_type_id);
                @endphp
                @if($accType)
                    <p class="text-sm text-gray-600 mt-1">Minimum balance: Rs. {{ number_format($accType->min_balance, 2) }}</p>
                @endif
            @endif
            <input type="hidden" wire:model="account_type_id" />
        </div>

        <!-- Branch -->
        <x-mary-select label="Branch" wire:model="branch_id"
            :options="$branches"
            option-label="name"
            option-value="id"
            placeholder="Select branch"
            required />

        <!-- Initial Balance -->
        <x-mary-input label="Initial Deposit" wire:model="balance" type="number" step="0.01" min="0" required />


        <!-- Opened Date -->
        <x-mary-input label="Opened Date" wire:model="opened_date" type="date" required />

    </div>

    <!-- Button -->
    <x-slot:actions>
        <x-mary-button
            label="Create Savings Account"
            class="btn-primary"
            type="submit" />
    </x-slot:actions>
</x-mary-form>
