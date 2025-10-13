<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen font-sans antialiased">
        <x-mary-nav sticky full-width>
            <x-slot:brand>
                <a href="{{ route('home') }}" wire:navigate>
                    <x-app-logo class="h-10 w-auto" />
                </a>
            </x-slot:brand>

            <x-slot:actions>
                <x-mary-dropdown>
                    <x-slot:trigger>
                        <x-mary-button icon="o-user-circle" class="btn-ghost btn-sm">
                            {{ auth()->user()->name }}
                        </x-mary-button>
                    </x-slot:trigger>

                    <x-mary-menu-item title="Settings" icon="o-cog-6-tooth" link="{{ route('settings.profile') }}" />
                    <x-mary-menu-separator />
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-mary-menu-item title="Log Out" icon="o-arrow-right-on-rectangle" onclick="this.closest('form').submit()" />
                    </form>
                </x-mary-dropdown>
            </x-slot:actions>
        </x-mary-nav>

        <x-mary-main with-nav full-width>
            <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200">
                <x-mary-menu activate-by-route>
                    <x-mary-menu-item title="Dashboard" icon="o-home" link="{{ route('dashboard') }}" />

                    <x-mary-menu-separator />

                    <x-mary-menu-sub title="Customer Management" icon="o-users">
                        <x-mary-menu-item title="Create Customer" icon="o-user-plus" link="{{ route('create.customer') }}" />
                        <x-mary-menu-item title="View Customers" icon="o-user-group" link="#" />
                    </x-mary-menu-sub>

                    <x-mary-menu-sub title="Accounts" icon="o-credit-card">
                        <x-mary-menu-item title="Create Savings Account" icon="o-plus-circle" link="#" />
                        <x-mary-menu-item title="Create Fixed Deposit" icon="o-banknotes" link="{{ route('create.fd') }}" />
                        <x-mary-menu-item title="View Accounts" icon="o-list-bullet" link="#" />
                    </x-mary-menu-sub>

                    <x-mary-menu-sub title="Transactions" icon="o-currency-dollar">
                        <x-mary-menu-item title="New Transaction" icon="o-arrow-path" link="{{ route('create.transaction') }}" />
                        <x-mary-menu-item title="Transaction History" icon="o-clock" link="#" />
                    </x-mary-menu-sub>

                    <x-mary-menu-sub title="Configuration" icon="o-cog-6-tooth">
                        <x-mary-menu-item title="Add Savings Account Type" icon="o-document-plus" link="{{ route('sv.add') }}" />
                        <x-mary-menu-item title="Add FD Type" icon="o-document-plus" link="{{ route('fd.add') }}" />
                    </x-mary-menu-sub>

                    <x-mary-menu-separator />

                    <x-mary-menu-sub title="Branch Management" icon="o-building-office">
                        <x-mary-menu-item title="Create Branch" icon="o-plus-circle" link="{{ route('create.branch') }}" />
                        <x-mary-menu-item title="View Branches" icon="o-list-bullet" link="#" />
                    </x-mary-menu-sub>

                    <x-mary-menu-sub title="Employee Management" icon="o-identification">
                        <x-mary-menu-item title="Create Employee" icon="o-user-plus" link="{{ route('create.employee') }}" />
                        <x-mary-menu-item title="View Employees" icon="o-user-group" link="#" />
                    </x-mary-menu-sub>

                    <x-mary-menu-separator />

                    <x-mary-menu-sub title="Reports" icon="o-chart-bar">
                        <x-mary-menu-item title="Customer Reports" icon="o-document-chart-bar" link="#" />
                        <x-mary-menu-item title="Transaction Reports" icon="o-currency-dollar" link="#" />
                        <x-mary-menu-item title="Interest Reports" icon="o-calculator" link="#" />
                    </x-mary-menu-sub>
                </x-mary-menu>
            </x-slot:sidebar>

            <x-slot:content>
                <div class="p-4 lg:p-8 bg-base-100">
                    {{ $slot }}
                </div>
            </x-slot:content>
        </x-mary-main>
    </body>
</html>
