<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// ==================== Public Routes ====================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ==================== Manager Only Routes ====================
// Managers can create branches, add branch managers, and add agents
Route::middleware(['auth', 'employee.manager'])->group(function () {
    // Branch Management
    Route::get('/create_branch', function () {
        return view('create-branch');
    })->name('create.branch');

    // Employee Management - Create any employee type
    Route::get('/create_employee', function () {
        return view('create-employee');
    })->name('create.employee');

    // Account Type Management (System Configuration)
    Route::get('/add_savings_acc_type', function () {
        return view('add_savings_acc_type');
    })->name('sv.add');

    Route::get('/add_fd_type', function () {
        return view('add_fd_type');
    })->name('fd.add');
});

// ==================== Branch Manager & Manager Routes ====================
// Branch Managers and Managers can view reports, stats, and agent activity
Route::middleware(['auth', 'employee.branch_manager'])->group(function () {
    // Reports & Statistics
    Route::get('/interest_reports', function () {
        return view('interest-reports');
    })->name('reports.interest');

    Route::get('/employee_reports', function () {
        return view('employee-reports');
    })->name('reports.emp');

    Route::get('/employee_wise_customers/{employee}', function (\App\Models\Employee $employee) {
        return view('employee-wise-customers', compact('employee'));
    })->name('reports.empcus');

    // View all employees (for management purposes)
    Route::get('/view_employees', function () {
        return view('view-employees');
    })->name('employees.view');

    // View all branches
    Route::get('/view_branches', function () {
        return view('view-branches');
    })->name('branches.view');

    // View all transactions (for oversight)
    Route::get('/view_transactions', function () {
        return view('view-transactions');
    })->name('transactions.view');
  
    Route::get('/customer_reports', function () {
        return view('customer-reports');
    })->name('reports.customer');

    Route::get('/report_transaction', function () {
    return view('transaction-report');
})->name('transactions.report');
});


// ==================== All Authenticated Employee Routes ====================
// Routes accessible to all logged-in employees (Manager, Branch Manager, Agent)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Customer Management
    // Note: Agents can only view/manage their assigned customers (enforced in view/controller)
    Route::get('/create_customer', function () {
        return view('create-customer');
    })->name('create.customer');

    Route::get('/view_customers', function () {
        return view('view-customers');
    })->name('customers.view');

    Route::get('/customer/{customer}', function (\App\Models\Customer $customer) {
        // Authorization: Agents can only view their assigned customers
        if (auth()->user()->canOnlyManageAssignedCustomers() && $customer->employee_id !== auth()->id()) {
            abort(403, 'Unauthorized. You can only view customers assigned to you.');
        }

        return view('user-details', ['customer' => $customer]);
    })->name('customers.details');

    // Savings Account Management
    Route::get('/create_savings_account', function () {
        return view('create-savings-account');
    })->name('create.savings.account');

    Route::get('/view_accounts', function () {
        return view('view-accounts');
    })->name('accounts.view');

    Route::get('/account/{account}', function (\App\Models\SavingsAccount $account) {
        // Authorization: Agents can only view accounts belonging to their assigned customers
        if (auth()->user()->canOnlyManageAssignedCustomers()) {
            $assignedCustomerIds = \App\Models\Customer::where('employee_id', auth()->id())->pluck('id');
            $accountCustomerIds = $account->customers->pluck('id');

            // Check if any of the account's customers are assigned to this agent
            if ($assignedCustomerIds->intersect($accountCustomerIds)->isEmpty()) {
                abort(403, 'Unauthorized. You can only view accounts belonging to your assigned customers.');
            }
        }

        return view('account-details', ['account' => $account]);
    })->name('accounts.details');

    // Fixed Deposit Management
    Route::get('/create_fd', function () {
        return view('create-fd');
    })->name('create.fd');

    Route::get('/fd_accounts', function () {
        return view('fd-accounts');
    })->name('fd.accounts');

    // Transactions
    Route::get('/create_transaction', function () {
        return view('create-transaction');
    })->name('create.transaction');

    // Savings Account Transactions
    Route::get('/saving_transfer', function () {
        return view('transaction/savings_transfer');
    })->name('sv.trans');

    Route::get('/saving_deposit', function () {
        return view('transaction/savings_deposit');
    })->name('sv.dep');

    Route::get('/saving_withdraw', function () {
        return view('transaction/savings_withdraw');
    })->name('sv.wit');

    // Settings Routes
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
