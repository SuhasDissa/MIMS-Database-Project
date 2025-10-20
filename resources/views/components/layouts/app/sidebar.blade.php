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
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <span class="badge badge-sm badge-primary ml-2">{{ auth()->user()->position->label() }}</span>
                        </x-mary-button>
                    </x-slot:trigger>

                    <div class="px-4 py-2 text-sm">
                        <div class="font-semibold">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ auth()->user()->position->label() }}</div>
                        <div class="text-xs text-gray-400">{{ auth()->user()->email }}</div>
                    </div>
                    <x-mary-menu-separator />
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
                    {{-- Dashboard - All Employees --}}
                    <x-mary-menu-item title="Dashboard" icon="o-home" link="{{ route('dashboard') }}" />

                    <x-mary-menu-separator />

                    {{-- Customer Management - All Employees --}}
                    <x-mary-menu-sub title="Customer Management" icon="o-users">
                        <x-mary-menu-item title="Create Customer" icon="o-user-plus" link="{{ route('create.customer') }}" />
                        <x-mary-menu-item title="View Customers" icon="o-user-group" link="{{ route('customers.view') }}" />
                    </x-mary-menu-sub>

                    {{-- Accounts - All Employees --}}
                    <x-mary-menu-sub title="Accounts" icon="o-credit-card">
                        <x-mary-menu-item title="Create Savings Account" icon="o-plus-circle" link="{{ route('create.savings.account') }}" />
                        <x-mary-menu-item title="Create Fixed Deposit" icon="o-banknotes" link="{{ route('create.fd') }}" />
                        <x-mary-menu-item title="View Accounts" icon="o-list-bullet" link="{{ route('accounts.view') }}" />
                        <x-mary-menu-item title="View Fixed Deposits" icon="o-banknotes" link="{{ route('fd.accounts') }}" />
                    </x-mary-menu-sub>

                    {{-- Transactions - All Employees --}}
                    <x-mary-menu-sub title="Transactions" icon="o-currency-dollar">
                        <x-mary-menu-item title="Deposit" icon="o-arrow-down-tray" link="{{ route('sv.dep') }}" />
                        <x-mary-menu-item title="Withdraw" icon="o-arrow-up-tray" link="{{ route('sv.wit') }}" />
                        <x-mary-menu-item title="Transfer" icon="o-arrow-path" link="{{ route('sv.trans') }}" />
                        @if(auth()->user()->canViewBranchStats())
                            <x-mary-menu-item title="Transaction History" icon="o-clock" link="{{ route('transactions.view') }}" />
                        @endif
                    </x-mary-menu-sub>

                    {{-- Configuration - Manager Only --}}
                    @if(auth()->user()->isManager())
                        <x-mary-menu-separator />

                        <x-mary-menu-sub title="System Configuration" icon="o-cog-6-tooth">
                            <x-mary-menu-item title="Add Savings Account Type" icon="o-document-plus" link="{{ route('sv.add') }}" />
                            <x-mary-menu-item title="Add FD Type" icon="o-document-plus" link="{{ route('fd.add') }}" />
                        </x-mary-menu-sub>
                    @endif

                    {{-- Branch Management - Manager Only (View for Branch Manager) --}}
                    @if(auth()->user()->isManager())
                        <x-mary-menu-separator />

                        <x-mary-menu-sub title="Branch Management" icon="o-building-office">
                            <x-mary-menu-item title="Create Branch" icon="o-plus-circle" link="{{ route('create.branch') }}" />
                            <x-mary-menu-item title="View Branches" icon="o-list-bullet" link="{{ route('branches.view') }}" />
                        </x-mary-menu-sub>
                    @elseif(auth()->user()->isBranchManager())
                        <x-mary-menu-separator />

                        <x-mary-menu-item title="View Branches" icon="o-building-office" link="{{ route('branches.view') }}" />
                    @endif

                    {{-- Employee Management - Manager Only (View for Branch Manager) --}}
                    @if(auth()->user()->isManager())
                        <x-mary-menu-sub title="Employee Management" icon="o-identification">
                            <x-mary-menu-item title="Create Employee" icon="o-user-plus" link="{{ route('create.employee') }}" />
                            <x-mary-menu-item title="View Employees" icon="o-user-group" link="{{ route('employees.view') }}" />
                        </x-mary-menu-sub>
                    @elseif(auth()->user()->isBranchManager())
                        <x-mary-menu-item title="View Employees" icon="o-identification" link="{{ route('employees.view') }}" />
                    @endif

                    {{-- Reports - Manager & Branch Manager Only --}}
                    @if(auth()->user()->canGenerateReports())
                        <x-mary-menu-separator />

                        <x-mary-menu-sub title="Reports & Analytics" icon="o-chart-bar">
                            <x-mary-menu-item title="Employee Reports" icon="o-document-chart-bar" link="{{ route('reports.emp') }}" />
                            <x-mary-menu-item title="Interest Reports" icon="o-calculator" link="{{ route('reports.interest') }}" />
                        </x-mary-menu-sub>
                    @endif
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
