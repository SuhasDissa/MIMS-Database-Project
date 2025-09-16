<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedDeposit extends Model
{
    protected $table = 'fixed_deposits';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'fd_number',
        'customer_id',
        'fd_type_id',
        'branch_id',
        'linked_account_id',
        'principal_amount',
        'interest_freq',
        'maturity_amount',
        'start_date',
        'maturity_date',
        'status',
        'interest_payout_option',
        'auto_renewal',
        'created_at',
        'closed_date',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'maturity_amount' => 'decimal:2',
        'start_date' => 'date',
        'maturity_date' => 'date',
        'auto_renewal' => 'boolean',
        'created_at' => 'datetime',
        'closed_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function fdType()
    {
        return $this->belongsTo(FixedDepositType::class, 'fd_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function transactions()
    {
        return $this->hasMany(FdTransaction::class, 'fd_acc_id');
    }

    public function interestCalculations()
    {
        return $this->hasMany(FdInterestCalculation::class, 'account_id');
    }

    public function maturityActions()
    {
        return $this->hasMany(FdMaturityAction::class, 'fd_id');
    }
}