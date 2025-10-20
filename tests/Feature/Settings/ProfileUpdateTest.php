<?php

use App\Models\Employee;
use Livewire\Volt\Volt;

test('profile page is displayed', function () {
    $this->actingAs($employee = Employee::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($employee);

    $response = Volt::test('settings.profile')
        ->set('first_name', 'Test')
        ->set('last_name', 'Employee')
        ->set('phone', '0771234567')
        ->set('position', 'Manager')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $employee->refresh();

    expect($employee->first_name)->toEqual('Test');
    expect($employee->last_name)->toEqual('Employee');
    expect($employee->email)->toEqual('test@example.com');
    expect($employee->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($employee);

    $response = Volt::test('settings.profile')
        ->set('first_name', $employee->first_name)
        ->set('last_name', $employee->last_name)
        ->set('phone', $employee->phone)
        ->set('position', $employee->position)
        ->set('email', $employee->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($employee->refresh()->email_verified_at)->not->toBeNull();
});

test('employee can delete their account', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($employee);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($employee->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($employee);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($employee->fresh())->not->toBeNull();
});