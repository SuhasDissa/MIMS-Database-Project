<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerStatusType;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\SavingsAccount;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\CustomerStatusTypesSeeder;
use Database\Seeders\SavingsAccountTypesSeeder;
use Database\Seeders\FixedDepositTypesSeeder;
use Database\Seeders\FixedDepositsSeeder;



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
        $this->call(FixedDepositsSeeder::class);

        // Use automated interest calculation commands instead of manual seeder
        $this->command->info('Calculating historical interest for savings accounts...');
        $this->calculateHistoricalInterest();

        $this->command->info('Calculating historical interest for fixed deposits...');
        $this->calculateHistoricalFdInterest();
    }

    /**
     * Calculate and credit historical interest for savings accounts
     */
    private function calculateHistoricalInterest(): void
    {
        // Calculate interest for the last 3 months and credit them
        for ($i = 3; $i >= 1; $i--) {
            $month = now()->subMonths($i)->format('Y-m');

            $this->command->info("  Processing month: {$month}");

            // Calculate interest
            Artisan::call('interest:calculate-savings', [
                '--month' => $month,
            ]);

            // Credit interest
            Artisan::call('interest:credit-savings', [
                '--month' => $month,
            ]);
        }

        $this->command->info('✓ Historical savings interest calculated and credited');
    }

    /**
     * Calculate and credit historical interest for fixed deposits
     */
    private function calculateHistoricalFdInterest(): void
    {
        // Calculate FD interest for the last 3 months and credit them
        for ($i = 3; $i >= 1; $i--) {
            $month = now()->subMonths($i)->format('Y-m');

            $this->command->info("  Processing FD month: {$month}");

            // Calculate interest
            Artisan::call('interest:calculate-fd', [
                '--month' => $month,
            ]);

            // Credit interest
            Artisan::call('interest:credit-fd', [
                '--month' => $month,
            ]);
        }

        $this->command->info('✓ Historical FD interest calculated and credited');
    }
}
