<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'id_type',
        'id_number',
        'status_id',
        'branch_id',
    ];

    protected $casts = [
        'date_of_birth' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function savingsAccounts()
    {
        return $this->belongsToMany(SavingsAccount::class, 'savings_accounts_customers', 'customer_id', 'sav_acc_id');
    }

    public function fixedDeposits()
    {
        return $this->hasMany(FixedDeposit::class, 'customer_id');
    }
}