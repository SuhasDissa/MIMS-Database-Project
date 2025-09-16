<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerStatusType extends Model
{
    protected $table = 'customer_status_types';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'status_name',
        'description',
        'min_age',
        'max_age',
        'created_at',
    ];

    protected $casts = [
        'status_name' => 'string',
        'created_at' => 'datetime',
    ];

    public function savingsAccountTypes()
    {
        return $this->hasMany(SavingsAccountType::class, 'customer_status_id');
    }
}