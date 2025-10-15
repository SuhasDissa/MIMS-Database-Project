<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavingsAccountTypesSeeder extends Seeder
{
    public function run(): void
    {
        $accountTypes = [
            [
                'name' => 'Student Savings',
                'customer_status_id' => 1, // CHILD
                'min_balance' => 0.00,
                'interest_rate' => 0.0200,
                'description' => 'Savings account for customers under 18 years with no minimum balance requirement',
                'is_active' => true,
            ],
            [
                'name' => 'Regular Savings',
                'customer_status_id' => 2, // ADULT
                'min_balance' => 1000.00,
                'interest_rate' => 0.0350,
                'description' => 'Standard savings account for adult customers with competitive interest rates',
                'is_active' => true,
            ],
            [
                'name' => 'Senior Savings',
                'customer_status_id' => 3, // SENIOR
                'min_balance' => 500.00,
                'interest_rate' => 0.0450,
                'description' => 'Premium savings account for senior citizens with higher interest rates and lower minimum balance',
                'is_active' => true,
            ],
        ];

        foreach ($accountTypes as $accountType) {
            DB::table('savings_account_type')->updateOrInsert(
                [
                    'name' => $accountType['name'],
                    'customer_status_id' => $accountType['customer_status_id'],
                ],
                array_merge($accountType, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
