<?php

use App\Models\Employee;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $employee = Employee::factory()->create();

    Volt::test('auth.forgot-password')
        ->set('email', $employee->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($employee, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $employee = Employee::factory()->create();

    Volt::test('auth.forgot-password')
        ->set('email', $employee->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($employee, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $employee = Employee::factory()->create();

    Volt::test('auth.forgot-password')
        ->set('email', $employee->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($employee, ResetPassword::class, function ($notification) use ($employee) {
        $response = Volt::test('auth.reset-password', ['token' => $notification->token])
            ->set('email', $employee->email)
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('resetPassword');

        $response
            ->assertHasNoErrors()
            ->assertRedirect(route('login', absolute: false));

        return true;
    });
});