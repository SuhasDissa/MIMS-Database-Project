<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/reg_cus', function () {
    return view('Customer_registration');
})->name('customer.register');

Route::get('/fd_open', function () {
    return view('FD_open');
})->name('fd.open');

Route::get('/add_savings_acc_type', function () {
    return view('add_savings_acc_type');
})->name('sv.add');


Route::get('/add_fd_type', function () {
    return view('add_fd_type');
})->name('fd.add');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
