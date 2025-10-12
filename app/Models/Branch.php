<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table = 'branch';

    protected $fillable = [
        'branch_code',
        'branch_name',
        'address',
        'city',
        'postal_code',
        'phone',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'branch_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    public function savingsAccounts()
    {
        return $this->hasMany(SavingsAccount::class, 'branch_id');
    }

    public function fixedDeposits()
    {
        return $this->hasMany(FixedDeposit::class, 'branch_id');
    }
}