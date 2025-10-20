<?php

use App\Enums\EmployeePosition;
use App\Models\Employee;

// ==================== Manager-Only Routes Tests ====================

test('manager can access create branch page', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/create_branch')
        ->assertStatus(200);
});

test('branch manager cannot access create branch page', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/create_branch')
        ->assertStatus(403);
});

test('agent cannot access create branch page', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/create_branch')
        ->assertStatus(403);
});

test('manager can access create employee page', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/create_employee')
        ->assertStatus(200);
});

test('branch manager cannot access create employee page', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/create_employee')
        ->assertStatus(403);
});

test('agent cannot access create employee page', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/create_employee')
        ->assertStatus(403);
});

test('manager can access add savings account type page', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/add_savings_acc_type')
        ->assertStatus(200);
});

test('agent cannot access add savings account type page', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/add_savings_acc_type')
        ->assertStatus(403);
});

test('manager can access add fd type page', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/add_fd_type')
        ->assertStatus(200);
});

test('agent cannot access add fd type page', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/add_fd_type')
        ->assertStatus(403);
});

// ==================== Branch Manager & Manager Routes Tests ====================

test('manager can access employee reports', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/employee_reports')
        ->assertStatus(200);
});

test('branch manager can access employee reports', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/employee_reports')
        ->assertStatus(200);
});

test('agent cannot access employee reports', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/employee_reports')
        ->assertStatus(403);
});

test('manager can access interest reports', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/interest_reports')
        ->assertStatus(200);
});

test('branch manager can access interest reports', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/interest_reports')
        ->assertStatus(200);
});

test('agent cannot access interest reports', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/interest_reports')
        ->assertStatus(403);
});

test('manager can view all employees', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/view_employees')
        ->assertStatus(200);
});

test('branch manager can view all employees', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/view_employees')
        ->assertStatus(200);
});

test('agent cannot view all employees', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/view_employees')
        ->assertStatus(403);
});

test('manager can view all branches', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/view_branches')
        ->assertStatus(200);
});

test('branch manager can view all branches', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/view_branches')
        ->assertStatus(200);
});

test('agent cannot view all branches', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/view_branches')
        ->assertStatus(403);
});

test('manager can view all transactions', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/view_transactions')
        ->assertStatus(200);
});

test('branch manager can view all transactions', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/view_transactions')
        ->assertStatus(200);
});

test('agent cannot view all transactions', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/view_transactions')
        ->assertStatus(403);
});

// ==================== All Authenticated Employee Routes Tests ====================

test('manager can access dashboard', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);

    $this->actingAs($manager)
        ->get('/dashboard')
        ->assertStatus(200);
});

test('branch manager can access dashboard', function () {
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);

    $this->actingAs($branchManager)
        ->get('/dashboard')
        ->assertStatus(200);
});

test('agent can access dashboard', function () {
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($agent)
        ->get('/dashboard')
        ->assertStatus(200);
});

test('all employees can access create customer page', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($manager)->get('/create_customer')->assertStatus(200);
    $this->actingAs($branchManager)->get('/create_customer')->assertStatus(200);
    $this->actingAs($agent)->get('/create_customer')->assertStatus(200);
});

test('all employees can access view customers page', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($manager)->get('/view_customers')->assertStatus(200);
    $this->actingAs($branchManager)->get('/view_customers')->assertStatus(200);
    $this->actingAs($agent)->get('/view_customers')->assertStatus(200);
});

test('all employees can access savings account management', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($manager)->get('/create_savings_account')->assertStatus(200);
    $this->actingAs($branchManager)->get('/view_accounts')->assertStatus(200);
    $this->actingAs($agent)->get('/create_savings_account')->assertStatus(200);
});

test('all employees can access transaction pages', function () {
    $manager = Employee::factory()->create(['position' => EmployeePosition::MANAGER]);
    $branchManager = Employee::factory()->create(['position' => EmployeePosition::BRANCH_MANAGER]);
    $agent = Employee::factory()->create(['position' => EmployeePosition::AGENT]);

    $this->actingAs($manager)->get('/saving_deposit')->assertStatus(200);
    $this->actingAs($branchManager)->get('/saving_withdraw')->assertStatus(200);
    $this->actingAs($agent)->get('/saving_transfer')->assertStatus(200);
});

// ==================== Unauthenticated Access Tests ====================

test('unauthenticated users cannot access protected routes', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/create_branch')->assertRedirect('/login');
    $this->get('/employee_reports')->assertRedirect('/login');
    $this->get('/create_customer')->assertRedirect('/login');
});

test('unauthenticated users can access public routes', function () {
    $this->get('/')->assertStatus(200);
});
