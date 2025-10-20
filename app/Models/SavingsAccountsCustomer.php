
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsAccountsCustomer extends Model
{
    protected $table = 'savings_accounts_customers';

    public $timestamps = false;

    protected $fillable = [
        'sav_acc_id',
        'customer_id',
    ];
}