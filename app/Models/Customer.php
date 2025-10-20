<?php

namespace App\Models;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'id_type',
        'id_number',
        'status_id',
        'employee_id',
        'branch_id',
    ];

    protected $casts = [
        'date_of_birth' => 'datetime',
        'gender' => GenderEnum::class,
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function savingsAccounts()
    {
        return $this->belongsToMany(SavingsAccount::class, 'savings_accounts_customers', 'customer_id', 'sav_acc_id');
    }

    public function fixedDeposits()
    {
        return $this->hasMany(FixedDeposit::class, 'customer_id');
    }

    public function status()
    {
        return $this->belongsTo(CustomerStatusType::class, 'status_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}