<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionMethodEnum;
use Illuminate\Database\Eloquent\Model;

class FdTransaction extends Model
{
    protected $table = 'fd_transaction';

    protected $fillable = [
        'type',
        'method',
        'fd_acc_id',
        'amount',
        'description',
        'balance_before',
        'balance_after',
        'updated_at',
    ];

    protected $casts = [
        'type' => TransactionTypeEnum::class,
        'method' => TransactionMethodEnum::class,
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fixedDeposit()
    {
        return $this->belongsTo(FixedDeposit::class, 'fd_acc_id');
    }

    public function maturityActions()
    {
        return $this->hasMany(FdMaturityAction::class, 'transaction_id');
    }
}