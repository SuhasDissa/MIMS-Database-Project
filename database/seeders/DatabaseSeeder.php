<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerStatusType;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        CustomerStatusType::factory()->createMany([
            ['status_name' => 'Adult', 'description' => 'Adult Customer', 'min_age' => 18, 'max_age' => 60],
            ['status_name' => 'Senior', 'description' => 'Senior Customer', 'min_age' => 60, 'max_age' => 180],
            ['status_name' => 'Junior', 'description' => 'Junior Customer', 'min_age' => 0, 'max_age' => 18],
       ]);


    }
}
