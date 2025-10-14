<x-mary-form wire:submit="save">
    <div class="grid grid-cols-1 gap-6">
        <x-mary-select label="Transaction Type" wire:model.live="type" :options="$transactionTypes" option-value="id" option-label="name" class="text-base w-[300px]" />

        @if ($type === 'WITHDRAWAL' || $type === 'TRANSFER')
            <x-mary-select label="From Account" wire:model="from_id" type="number"  class="text-base w-[300px]" />
        @endif

        @if ($type === 'DEPOSIT' || $type === 'TRANSFER')
            <x-mary-select label="To Account" wire:model="to_id" :options="$accounts" option-value="id" option-label="name" class="text-base w-[300px]" />
        @endif

        <x-mary-input label="Amount" wire:model="amount" type="number" min="0" class="text-base w-[300px]" />

        <x-mary-textarea label="Description" wire:model="description" rows="3" class="text-base w-[300px]" />
    </div>

    <x-slot:actions>
        <div class="mt-6 w-full">
            <x-mary-button label="Proceed Transaction"
            class="btn-primary w-full py-6 text-base border-white/50 rounded-lg shadow-md bg-transparent transition-all duration-200 hover:border-white/100 hover:bg-white  hover:text-black font-semibold"/>
        </div>
    </x-slot:actions>
</x-mary-form>