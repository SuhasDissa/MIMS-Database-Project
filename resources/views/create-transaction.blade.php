<x-layouts.auth.clean>
    <div class="flex h-[100dvh] w-full items-center justify-center m-0 p-0">
        <div class="w-1/2 h-full flex items-start justify-center">
            <img src="{{ asset('bank3.png') }}" alt="Transaction" class="w-full h-full object-cover">
        </div>
        <div class="p-6 w-1/2 flex items-center justify-center flex-col">
            <h2 class="text-4xl font-bold text-white-800 mb-10 text-center">
                New Transaction
            </h2>

            <div x-data="{ tab: 'savings' }" class="p-6 w-[50%] flex items-center justify-center flex-col">
                {{-- <div class="flex  mb-10 ">
                    <button @click="tab = 'savings'" :class="{ 'border-b-2 border-blue-500': tab === 'savings' }" class="px-4 py-2 text-base text-white-500">
                        Savings<nobr> Transaction
                    </button>
                    <button @click="tab = 'fd'" :class="{ 'border-b-2 border-blue-500': tab === 'fd' }" class="px-4 py-2 text-base text-white-500">
                        Fixed Deposit<nobr> Transaction
                    </button>
                </div> --}}

                <div x-show="tab === 'savings'">
                    <livewire:savings-transaction-form />
                </div>
                {{-- <div x-show="tab === 'fd'" style="display: none;">
                    <livewire:fd-transaction-form /> --}}
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth.clean>
