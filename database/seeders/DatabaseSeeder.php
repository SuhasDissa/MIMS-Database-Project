<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerStatusType;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\SavingsAccount;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CustomerStatusTypesSeeder;
use Database\Seeders\SavingsAccountTypesSeeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //1
        $this->call(CustomerStatusTypesSeeder::class);
        //2
        Branch::factory(10)->create();
        //3
        $this->call(SavingsAccountTypesSeeder::class);
        //4
        Customer::factory(10)->create();
        //5
        SavingsAccount::factory(5)->create();

            // User::factory()->create([
            //     'name' => 'Test User',
            //     'email' => 'test@example.com',
            // ]);

        // User::factory(10)->create();
        CustomerStatusType::factory()->createMany([
            ['status_name' => 'Adult', 'description' => 'Adult Customer', 'min_age' => 18, 'max_age' => 60],
            ['status_name' => 'Senior', 'description' => 'Senior Customer', 'min_age' => 60, 'max_age' => 180],
            ['status_name' => 'Junior', 'description' => 'Junior Customer', 'min_age' => 0, 'max_age' => 18],
       ]);


    }
}
