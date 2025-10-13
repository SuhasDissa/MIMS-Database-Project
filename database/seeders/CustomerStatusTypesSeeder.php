<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerStatusTypesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customer_status_types')->insert([
            [
                'status_name' => 'CHILD',
                'description' => 'Customers under 18 years old',
                'min_age' => 0,
                'max_age' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'ADULT',
                'description' => 'Regular adult customers',
                'min_age' => 18,
                'max_age' => 59,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_name' => 'SENIOR',
                'description' => 'Senior citizen customers',
                'min_age' => 60,
                'max_age' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
