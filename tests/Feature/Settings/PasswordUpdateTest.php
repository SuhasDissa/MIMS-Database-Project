<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

test('password can be updated', function () {
    $employee = Employee::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($employee);

    $response = Volt::test('settings.password')
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('new-password', $employee->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $employee = Employee::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($employee);

    $response = Volt::test('settings.password')
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});