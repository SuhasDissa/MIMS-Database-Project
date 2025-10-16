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
use Database\Seeders\FixedDepositTypesSeeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Required seeders
        $this->call(CustomerStatusTypesSeeder::class);
        $this->call(SavingsAccountTypesSeeder::class);
        $this->call(FixedDepositTypesSeeder::class);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('demo'),
        ]);

        // Testing only seeders
        Branch::factory(10)->create();
        Customer::factory(100)->create();
        SavingsAccount::factory(500)->create();

        $this->call(SavingsTransactionsSeeder::class);
        $this->call(SavingsAccountInterestCalculationsSeeder::class);
    }
}
