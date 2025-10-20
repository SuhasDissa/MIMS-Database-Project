<?php

use App\Models\Employee;

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated employees can visit the dashboard', function () {
    $employee = Employee::factory()->create();
    $this->actingAs($employee);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});