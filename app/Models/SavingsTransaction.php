<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Model;

class SavingsTransaction extends Model
{
    protected $table = 'savings_transaction';

    protected $fillable = [
        'type',
        'from_id',
        'to_id',
        'amount',
        'status',
        'description',
        'balance_before',
        'balance_after',
        'updated_at',
    ];

    protected $casts = [
        'type' => TransactionTypeEnum::class,
        'status' => TransactionStatusEnum::class,
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(SavingsAccount::class, 'from_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(SavingsAccount::class, 'to_id');
    }
}