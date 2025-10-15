<?php

namespace App\Models;

use App\Enums\CustomerStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStatusType extends Model
{
    use HasFactory;
    protected $table = 'customer_status_types';

    protected $fillable = [
        'status_name',
        'description',
        'min_age',
        'max_age',
    ];

    protected $casts = [
        'status_name' => CustomerStatusEnum::class,
        'created_at' => 'datetime',
    ];

    public function savingsAccountTypes()
    {
        return $this->hasMany(SavingsAccountType::class, 'customer_status_id');
    }
}