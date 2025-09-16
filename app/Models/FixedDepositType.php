<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedDepositType extends Model
{
    protected $table = 'fixed_deposit_type';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'min_deposit',
        'interest_rate',
        'tenure_months',
        'description',
        'is_active',
        'created_at',
    ];

    protected $casts = [
        'min_deposit' => 'decimal:2',
        'interest_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function fixedDeposits()
    {
        return $this->hasMany(FixedDeposit::class, 'fd_type_id');
    }
}