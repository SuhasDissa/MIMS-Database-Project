<?php

namespace App\Models;

use App\Enums\InterestCalculationStatusEnum;
use Illuminate\Database\Eloquent\Model;

class SavingsAccountInterestCalculation extends Model
{
    protected $table = 'savings_account_interest_calculations';

    protected $fillable = [
        'account_id',
        'calculation_period_start',
        'calculation_period_end',
        'principal_amount',
        'interest_rate',
        'days_calculated',
        'interest_amount',
        'status',
        'calculation_date',
        'credited_date',
        'transaction_id',
    ];

    protected $casts = [
        'calculation_period_start' => 'date',
        'calculation_period_end' => 'date',
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:4',
        'interest_amount' => 'decimal:2',
        'status' => InterestCalculationStatusEnum::class,
        'calculation_date' => 'datetime',
        'credited_date' => 'datetime',
    ];

    public function savingsAccount()
    {
        return $this->belongsTo(SavingsAccount::class, 'account_id');
    }
}