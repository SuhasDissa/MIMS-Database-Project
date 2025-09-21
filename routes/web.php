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

Route::get('/create_employee', function () {
    return view('create-employee');
})->name('create.employee');

Route::get('/create_branch', function () {
    return view('branch-creation');
})->name('create.branch');

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
