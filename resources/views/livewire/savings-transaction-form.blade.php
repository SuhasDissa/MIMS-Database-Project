<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Deposit Card -->
    <x-mary-card title="Deposit Money" class="border border-gray-200 shadow-sm">
        <p class="text-gray-700 mb-4">
            Deposit funds into your saving account securely and quickly.
        </p>

        <x-slot:figure>
            <img src="{{ asset('money_deposit.png') }}" 
                 class="w-full h-48 object-cover rounded-lg shadow-sm" 
                 alt="Deposit Illustration"/>
        </x-slot:figure>

        <x-slot:actions separator>
            <x-mary-button label="Deposit Money" class="btn-primary px-6 py-2 text-white hover:bg-blue-700" onclick="window.location='{{ route('sv.dep') }}'"  />
        </x-slot:actions>
    </x-mary-card>

    <!-- Withdraw Card -->
    <x-mary-card title="Withdraw Money" class="border border-gray-200 shadow-sm">
        <p class="text-gray-700 mb-4">
            Withdraw funds from your saving account securely and quickly.
        </p>

        <x-slot:figure>
            <img src="{{ asset('money_withdraw.jpg') }}" 
                 class="w-full h-48 object-cover rounded-lg shadow-sm" 
                 alt="Withdraw Illustration"/>
        </x-slot:figure>


        <x-slot:actions separator>
            <x-mary-button label="Withdraw Money" class="btn-primary px-6 py-2 text-white hover:bg-blue-700" onclick="window.location='{{ route('sv.wit') }}'"  />
        </x-slot:actions>
    </x-mary-card>

    <!-- Transfer Card -->
    <x-mary-card title="Transfer Money" class="border border-gray-200 shadow-sm">
        <p class="text-gray-700 mb-4">
            Transfer funds from your saving account securely and quickly.
        </p>

        <x-slot:figure>
            <img src="{{ asset('money_transfer.jpg') }}" 
                 class="w-full h-48 object-cover rounded-lg shadow-sm" 
                 alt="Transfer Illustration"/>
        </x-slot:figure>

        <x-slot:actions separator>
            <x-mary-button label="Transfer Money" class="btn-primary px-6 py-2 text-white hover:bg-blue-700"  onclick="window.location='{{ route('sv.trans') }}'" />
        </x-slot:actions>
    </x-mary-card>
</div>
