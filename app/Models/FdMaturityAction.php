<?php

namespace App\Models;

use App\Enums\MaturityActionEnum;
use Illuminate\Database\Eloquent\Model;

class FdMaturityAction extends Model
{
    protected $table = 'fd_maturity_actions';

    protected $fillable = [
        'fd_id',
        'maturity_date',
        'action_taken',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'new_fd_id',
        'transaction_id',
        'processed_date',
    ];

    protected $casts = [
        'maturity_date' => 'datetime',
        'action_taken' => MaturityActionEnum::class,
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'processed_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function fixedDeposit()
    {
        return $this->belongsTo(FixedDeposit::class, 'fd_id');
    }

    public function newFixedDeposit()
    {
        return $this->belongsTo(FixedDeposit::class, 'new_fd_id');
    }

    public function transaction()
    {
        return $this->belongsTo(FdTransaction::class, 'transaction_id');
    }
}