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
                'name' => 'Children',
                'customer_status_id' => 1, // CHILD (0-11 years)
                'min_balance' => 0.00,
                'interest_rate' => 0.12, // 12% interest
                'description' => 'Savings account for children aged 0-11 years with no minimum balance requirement',
                'is_active' => true,
            ],
            [
                'name' => 'Teen',
                'customer_status_id' => 2, // TEEN (12-17 years)
                'min_balance' => 500.00,
                'interest_rate' => 0.11, // 11% interest, minimum LKR 500
                'description' => 'Savings account for teens aged 12-17 years with minimum balance of LKR 500',
                'is_active' => true,
            ],
            [
                'name' => 'Adult',
                'customer_status_id' => 3, // ADULT (18-59 years)
                'min_balance' => 1000.00,
                'interest_rate' => 0.10, // 10% interest, minimum LKR 1000
                'description' => 'Standard savings account for adult customers aged 18+ with minimum balance of LKR 1000',
                'is_active' => true,
            ],
            [
                'name' => 'Senior',
                'customer_status_id' => 4, // SENIOR (60+ years)
                'min_balance' => 1000.00,
                'interest_rate' => 0.13, // 13% interest, minimum LKR 1000
                'description' => 'Premium savings account for senior citizens aged 60+ with higher interest rates',
                'is_active' => true,
            ],
            [
                'name' => 'Joint',
                'customer_status_id' => null, // No specific age restriction for joint accounts
                'min_balance' => 5000.00,
                'interest_rate' => 0.07, // 7% interest, minimum LKR 5000
                'description' => 'Joint savings account for multiple account holders with minimum balance of LKR 5000',
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
