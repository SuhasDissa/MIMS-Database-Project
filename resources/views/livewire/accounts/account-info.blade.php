<?php

use App\Models\SavingsAccount;
use Livewire\Volt\Component;

new class extends Component {
    public SavingsAccount $account;

    public function mount(): void
    {
        $this->account->load(['accountType', 'branch', 'customers']);
    }
}; ?>

<div>
    {{-- Back Button --}}
    <div class="mb-4">
        <x-mary-button
            label="Back to Accounts"
            icon="o-arrow-left"
            link="{{ route('accounts.view') }}"
            class="btn-ghost btn-sm" />
    </div>

    {{-- Page Header --}}
    <x-mary-header
        title="Account Details"
        subtitle="Account Number: {{ $account->account_number }}"
        separator />

    {{-- Account Information Cards --}}
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 mt-6">
        {{-- Balance Card --}}
        <x-mary-card shadow>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-success/10 rounded-lg">
                    <x-mary-icon name="o-banknotes" class="w-8 h-8 text-success" />
                </div>
                <div>
                    <div class="text-sm text-gray-500">Current Balance</div>
                    <div class="text-2xl font-bold text-success">Rs. {{ number_format($account->balance, 2) }}</div>
                </div>
            </div>
        </x-mary-card>

        {{-- Account Type Card --}}
        <x-mary-card shadow>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary/10 rounded-lg">
                    <x-mary-icon name="o-credit-card" class="w-8 h-8 text-primary" />
                </div>
                <div>
                    <div class="text-sm text-gray-500">Account Type</div>
                    <div class="text-lg font-semibold">{{ $account->accountType->type_name }}</div>
                    <div class="text-xs text-gray-500">Rate: {{ number_format($account->accountType->interest_rate, 2) }}%</div>
                </div>
            </div>
        </x-mary-card>

        {{-- Status Card --}}
        <x-mary-card shadow>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-info/10 rounded-lg">
                    <x-mary-icon name="o-flag" class="w-8 h-8 text-info" />
                </div>
                <div>
                    <div class="text-sm text-gray-500">Status</div>
                    <div class="mt-1">
                        @if($account->status === 'ACTIVE')
                            <span class="badge badge-success badge-lg">{{ $account->status }}</span>
                        @elseif($account->status === 'DORMANT')
                            <span class="badge badge-warning badge-lg">{{ $account->status }}</span>
                        @else
                            <span class="badge badge-error badge-lg">{{ $account->status }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </x-mary-card>
    </div>

    {{-- Detailed Information --}}
    <div class="grid gap-6 md:grid-cols-2 mt-6">
        {{-- Account Details --}}
        <x-mary-card title="Account Information" shadow separator>
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Account Number</span>
                    <span class="font-mono font-semibold">{{ $account->account_number }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Branch</span>
                    <span class="font-semibold">{{ $account->branch->branch_name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Opened Date</span>
                    <span class="font-semibold">{{ $account->opened_date->format('F d, Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-sm text-gray-600">Last Transaction</span>
                    <span class="font-semibold">
                        {{ $account->last_transaction_date ? $account->last_transaction_date->format('F d, Y') : 'N/A' }}
                    </span>
                </div>
                @if($account->closed_date)
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-sm text-gray-600">Closed Date</span>
                        <span class="font-semibold text-error">{{ $account->closed_date->format('F d, Y') }}</span>
                    </div>
                @endif
            </div>
        </x-mary-card>

        {{-- Account Holders --}}
        <x-mary-card title="Account Holders" shadow separator>
            <div class="space-y-3">
                @foreach($account->customers as $customer)
                    <div class="flex items-center gap-3 p-3 bg-base-200 rounded-lg">
                        <div class="flex-1">
                            <div class="font-semibold">{{ $customer->first_name . ' ' . $customer->last_name }}</div>
                            <div class="text-sm text-gray-500">NIC: {{ $customer->id_number }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-mary-card>
    </div>
</div>
