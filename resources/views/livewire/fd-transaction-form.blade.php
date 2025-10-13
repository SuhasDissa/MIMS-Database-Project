<x-mary-form wire:submit="save">
    <div class="grid grid-cols-1 gap-6">
        <x-mary-select label="Transaction Type" wire:model="type" :options="$transactionTypes" option-value="id" option-label="name" class="text-base w-[300px]" />

        <x-mary-select label="Transaction Method" wire:model="method" :options="$transactionMethods" option-value="id" option-label="name"  class="text-base w-[300px]"/>

        <x-mary-select label="Fixed Deposit Account" wire:model="fd_acc_id" :options="$fixedDepositAccounts" option-value="id" option-label="name"  class="text-base w-[300px]"/>

        <x-mary-select label="Savings Account" wire:model="savings_account_id" :options="$savingsAccounts" option-value="id" option-label="name" class="text-base w-[300px]"/>

        <x-mary-input label="Amount" wire:model="amount" type="number" min="0" class="text-base w-[300px]"/>

        <x-mary-textarea label="Description" wire:model="description" rows="3" class="text-base w-[300px]"/>
    </div>

    <x-slot:actions>
        <div class="mt-6">
            <x-mary-button label="Create Transaction" 
            class="btn-primary w-full py-6 text-base border-white/50 rounded-lg shadow-md bg-transparent transition-all duration-200 hover:border-white/100 hover:bg-white  hover:text-black font-semibold"

            type="submit" />
        </div>
    </x-slot:actions>
</x-mary-form>