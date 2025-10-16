<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixedDepositTypesSeeder extends Seeder
{
    public function run(): void
    {
        $fdTypes = [
            [
                'name' => '6 Months',
                'min_deposit' => 1000.00,
                'interest_rate' => 0.13, // 13% interest
                'tenure_months' => 6,
                'description' => 'Fixed deposit for 6 months with 13% annual interest rate',
                'is_active' => true,
            ],
            [
                'name' => '1 Year',
                'min_deposit' => 1000.00,
                'interest_rate' => 0.14, // 14% interest
                'tenure_months' => 12,
                'description' => 'Fixed deposit for 1 year with 14% annual interest rate',
                'is_active' => true,
            ],
            [
                'name' => '3 Years',
                'min_deposit' => 1000.00,
                'interest_rate' => 0.15, // 15% interest
                'tenure_months' => 36,
                'description' => 'Fixed deposit for 3 years with 15% annual interest rate',
                'is_active' => true,
            ],
        ];

        foreach ($fdTypes as $fdType) {
            DB::table('fixed_deposit_type')->updateOrInsert(
                [
                    'name' => $fdType['name'],
                    'tenure_months' => $fdType['tenure_months'],
                ],
                array_merge($fdType, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
