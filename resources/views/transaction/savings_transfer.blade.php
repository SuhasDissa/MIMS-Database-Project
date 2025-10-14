<x-layouts.auth.clean>
    <div class=" flex h-[100dvh] w-[100%] items-center justify-center m-0 p-0">
                <div class="w-[50%] h-full flex items-start justify-center">
                    <img src="{{ asset('bank3.png') }}" alt="Customer Registration" class="w-full h-full">
                </div>
                <div class="p-6 w-[50%] flex items-center justify-center flex-col">
                    <!-- Title -->
                    <h2 class="text-4xl font-bold text-white-800 mb-10 text-center">
                        Transfer Money
                    </h2>

                    <!-- Form -->
                    <livewire:transaction.saving-transfer-form/>

                </div>
            </div>
</x-layouts.auth.clean>