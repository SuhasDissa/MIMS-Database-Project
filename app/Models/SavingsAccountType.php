<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsAccountType extends Model
{
    protected $table = 'savings_account_type';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'customer_status_id',
        'min_balance',
        'interest_rate',
        'description',
        'is_active',
        'created_at',
    ];

    protected $casts = [
        'min_balance' => 'decimal:2',
        'interest_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function customerStatusType()
    {
        return $this->belongsTo(CustomerStatusType::class, 'customer_status_id');
    }

    public function savingsAccounts()
    {
        return $this->hasMany(SavingsAccount::class, 'account_type_id');
    }
}