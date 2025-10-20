<?php

use App\Models\Employee;
use Livewire\Volt\Volt as LivewireVolt;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('employees can authenticate using the login screen', function () {
    $employee = Employee::factory()->create();

    $response = LivewireVolt::test('auth.login')
        ->set('email', $employee->email)
        ->set('password', 'password')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('employees can not authenticate with invalid password', function () {
    $employee = Employee::factory()->create();

    $response = LivewireVolt::test('auth.login')
        ->set('email', $employee->email)
        ->set('password', 'wrong-password')
        ->call('login');

    $response->assertHasErrors('email');

    $this->assertGuest();
});

test('employees can logout', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->post('/logout');

    $response->assertRedirect('/');

    $this->assertGuest();
});