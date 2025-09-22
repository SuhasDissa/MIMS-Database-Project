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

Route::get('/create_employee', function () {
    return view('create-employee');
})->name('create.employee');

Route::get('/create_branch', function () {
    return view('create-branch');
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
