<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FdInterestCalculation extends Model
{
    protected $table = 'fd_interest_calculations';
    public $timestamps = false;

    protected $fillable = [
        'id',
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
        'calculation_date' => 'datetime',
        'credited_date' => 'datetime',
    ];

    public function fixedDeposit()
    {
        return $this->belongsTo(FixedDeposit::class, 'account_id');
    }
}