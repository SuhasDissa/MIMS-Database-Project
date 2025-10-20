<?php

namespace App\Models;

use App\Enums\EmployeePosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'employee';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'nic_num',
        'branch_id',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'position' => EmployeePosition::class,
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'employee_id');
    }

    /**
     * Get the employee's full name
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the employee's initials
     */
    public function initials(): string
    {
        return Str::of($this->first_name)
            ->substr(0, 1)
            ->append(Str::of($this->last_name)->substr(0, 1))
            ->upper()
            ->toString();
    }

    // ==================== Authorization Methods ====================

    /**
     * Check if the employee is a Manager
     */
    public function isManager(): bool
    {
        return $this->position === EmployeePosition::MANAGER;
    }

    /**
     * Check if the employee is a Branch Manager
     */
    public function isBranchManager(): bool
    {
        return $this->position === EmployeePosition::BRANCH_MANAGER;
    }

    /**
     * Check if the employee is an Agent
     */
    public function isAgent(): bool
    {
        return $this->position === EmployeePosition::AGENT;
    }

    /**
     * Check if the employee can manage branches
     * Only MANAGER can create/manage branches
     */
    public function canManageBranches(): bool
    {
        return $this->isManager();
    }

    /**
     * Check if the employee can manage branch managers
     * Only MANAGER can add/manage branch managers
     */
    public function canManageBranchManagers(): bool
    {
        return $this->isManager();
    }

    /**
     * Check if the employee can manage agents
     * MANAGER and BRANCH_MANAGER can manage agents
     */
    public function canManageAgents(): bool
    {
        return $this->isManager() || $this->isBranchManager();
    }

    /**
     * Check if the employee can view branch statistics
     * MANAGER and BRANCH_MANAGER can view branch stats
     */
    public function canViewBranchStats(): bool
    {
        return $this->isManager() || $this->isBranchManager();
    }

    /**
     * Check if the employee can generate reports
     * MANAGER and BRANCH_MANAGER can generate reports
     */
    public function canGenerateReports(): bool
    {
        return $this->isManager() || $this->isBranchManager();
    }

    /**
     * Check if the employee can view agent activity
     * MANAGER and BRANCH_MANAGER can view agent activity
     */
    public function canViewAgentActivity(): bool
    {
        return $this->isManager() || $this->isBranchManager();
    }

    /**
     * Check if the employee can manage customers
     * All positions can manage customers (agents only their assigned ones)
     */
    public function canManageCustomers(): bool
    {
        return true;
    }

    /**
     * Check if the employee can only manage their assigned customers
     * Only AGENT is restricted to assigned customers
     */
    public function canOnlyManageAssignedCustomers(): bool
    {
        return $this->isAgent();
    }

    /**
     * Get the authorization level (hierarchical)
     * Returns: 3 for MANAGER, 2 for BRANCH_MANAGER, 1 for AGENT
     */
    public function getAuthLevel(): int
    {
        return match($this->position) {
            EmployeePosition::MANAGER => 3,
            EmployeePosition::BRANCH_MANAGER => 2,
            EmployeePosition::AGENT => 1,
        };
    }

    /**
     * Check if this employee has higher or equal authority than another employee
     */
    public function hasAuthorityOver(Employee $employee): bool
    {
        return $this->getAuthLevel() > $employee->getAuthLevel();
    }
}