<?php

use App\Models\Employee;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $employee = Employee::factory()->unverified()->create();

    $response = $this->actingAs($employee)->get('/verify-email');

    $response->assertStatus(200);
});

test('email can be verified', function () {
    $employee = Employee::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $employee->id, 'hash' => sha1($employee->email)]
    );

    $response = $this->actingAs($employee)->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($employee->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $employee = Employee::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $employee->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($employee)->get($verificationUrl);

    expect($employee->fresh()->hasVerifiedEmail())->toBeFalse();
});