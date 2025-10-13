<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavingsAccountTypesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('savings_account_type')->insert([
            [
                'name' => 'Regular Savings',
                'customer_status_id' => 2, // ADULT
                'min_balance' => 1000.00,
                'interest_rate' => 0.0350,
                'description' => 'For standard customers',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Senior Savings',
                'customer_status_id' => 3, // SENIOR
                'min_balance' => 500.00,
                'interest_rate' => 0.0450,
                'description' => 'Higher interest for seniors',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Student Savings',
                'customer_status_id' => 1, // CHILD or STUDENT
                'min_balance' => 0.00,
                'interest_rate' => 0.0200,
                'description' => 'No minimum balance required',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
