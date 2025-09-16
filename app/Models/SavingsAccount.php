<?php

namespace App\Models;

use App\Enums\AccountStatusEnum;
use Illuminate\Database\Eloquent\Model;

class SavingsAccount extends Model
{
    protected $table = 'savings_account';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'account_number',
        'account_type_id',
        'branch_id',
        'balance',
        'status',
        'opened_date',
        'closed_date',
        'last_transaction_date',
        'created_at',
        'last_updated_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'status' => AccountStatusEnum::class,
        'opened_date' => 'date',
        'closed_date' => 'date',
        'last_transaction_date' => 'date',
        'created_at' => 'datetime',
        'last_updated_at' => 'datetime',
    ];

    public function accountType()
    {
        return $this->belongsTo(SavingsAccountType::class, 'account_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'savings_accounts_customers', 'sav_acc_id', 'customer_id');
    }

    public function transactionsFrom()
    {
        return $this->hasMany(SavingsTransaction::class, 'from_id');
    }

    public function transactionsTo()
    {
        return $this->hasMany(SavingsTransaction::class, 'to_id');
    }

    public function interestCalculations()
    {
        return $this->hasMany(SavingsAccountInterestCalculation::class, 'account_id');
    }
}