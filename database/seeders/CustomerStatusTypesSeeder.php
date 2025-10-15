<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerStatusTypesSeeder extends Seeder
{
    public function run(): void
    {
        $statusTypes = [
            [
                'status_name' => 'CHILD',
                'description' => 'Customers under 18 years old - eligible for student accounts with no minimum balance',
                'min_age' => 0,
                'max_age' => 17,
            ],
            [
                'status_name' => 'ADULT',
                'description' => 'Regular adult customers aged 18-59 years',
                'min_age' => 18,
                'max_age' => 59,
            ],
            [
                'status_name' => 'SENIOR',
                'description' => 'Senior citizen customers aged 60+ with preferential interest rates',
                'min_age' => 60,
                'max_age' => null,
            ],
        ];

        foreach ($statusTypes as $statusType) {
            DB::table('customer_status_types')->updateOrInsert(
                ['status_name' => $statusType['status_name']],
                array_merge($statusType, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
