<x-layouts.auth.clean>
    <div class=" flex h-[100dvh] w-[100%] items-center justify-center m-0 p-0">
                <div class="min-w-[50%] h-full flex items-start justify-center">
                    <img src="{{ asset('bank.jpg') }}" alt="Customer Registration" class="w-full h-full sticky " />
                </div>
                <div class="py-16  w-[50%] flex items-center justify-center flex-col overflow-scroll">
                    <!-- Title -->
                    <h2 class="text-4xl font-bold text-base-content mb-10 mt-35 text-center">
                        Customer Registration
                    </h2>

                    <!-- Form -->
                    <livewire:customer-creation-form />
                </div>
            </div>
</x-layouts.auth.clean>