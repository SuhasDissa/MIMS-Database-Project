<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/create_customer', function () {
    return view('create-customer');
})->name('create.customer');

Route::get('/create_fd', function () {
    return view('create-fd');
})->name('create.fd');

Route::get('/create_savings_account', function () {
    return view('create-savings-account');
})->name('create.savings.account');

Route::get('/create_employee', function () {
    return view('create-employee');
})->name('create.employee');

Route::get('/create_branch', function () {
    return view('create-branch');
})->name('create.branch');

Route::get('/add_savings_acc_type', function () {
    return view('add_savings_acc_type');
})->name('sv.add');

Route::get('/add_fd_type', function () {
    return view('add_fd_type');
})->name('fd.add');


Route::get('/create_transaction', function () {
    return view('create-transaction');
})->name('create.transaction');

Route::get('/view_accounts', function () {
    return view('view-accounts');
})->name('accounts.view');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// Saving account Transactions
Route::middleware(['auth'])->group(function () {

    Route::get('/saving_transfer', function () {
        return view('transaction/savings_transfer');
    })->name('sv.trans');

    Route::get('/saving_deposit', function () {
        return view('transaction/savings_deposit');
    })->name('sv.dep');

    Route::get('/saving_withdraw', function () {
        return view('transaction/savings_withdraw');
    })->name('sv.wit');
    
});



Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
