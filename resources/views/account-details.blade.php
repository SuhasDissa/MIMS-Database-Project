<x-layouts.app>
    <div class="space-y-6">
        {{-- Account Details Component --}}

                <livewire:accounts.account-info :account="$account" />


        {{-- Transaction History Component --}}
 
                <livewire:accounts.transaction-history :account="$account" />
 

        {{-- Interest History Component --}}

                <livewire:accounts.interest-history :account="$account" />

    </div>
</x-layouts.app>
