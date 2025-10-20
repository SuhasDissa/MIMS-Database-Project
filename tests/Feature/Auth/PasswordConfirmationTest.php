<?php

use App\Models\Employee;
use Livewire\Volt\Volt;

test('confirm password screen can be rendered', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->get('/confirm-password');

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($employee);

    $response = Volt::test('auth.confirm-password')
        ->set('password', 'password')
        ->call('confirmPassword');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));
});

test('password is not confirmed with invalid password', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($employee);

    $response = Volt::test('auth.confirm-password')
        ->set('password', 'wrong-password')
        ->call('confirmPassword');

    $response->assertHasErrors(['password']);
});