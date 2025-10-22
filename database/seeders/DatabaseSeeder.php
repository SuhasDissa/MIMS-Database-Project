<?php

namespace Database\Seeders;

use App\Enums\EmployeePosition;
use App\Models\Employee;
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

        // Create test employee for authentication
        $testBranch = Branch::factory()->create();
        Employee::factory()->create([
            'first_name' => 'Main',
            'last_name' => 'Manager',
            'email' => 'manager@test.com',
            'password' => bcrypt('demo'),
            'phone' => '0771234567',
            'position' => EmployeePosition::MANAGER,
            'nic_num' => '200012345678',
            'branch_id' => $testBranch->id,
            'is_active' => true,
        ]);

        Employee::factory()->create([
            'first_name' => 'Branch',
            'last_name' => 'Manager',
            'email' => 'bmanager@test.com',
            'password' => bcrypt('demo'),
            'phone' => '0771234567',
            'position' => EmployeePosition::BRANCH_MANAGER,
            'nic_num' => '200012345678',
            'branch_id' => $testBranch->id,
            'is_active' => true,
        ]);

        Employee::factory()->create([
            'first_name' => 'Customer',
            'last_name' => 'Agent',
            'email' => 'agent@test.com',
            'password' => bcrypt('demo'),
            'phone' => '0771234567',
            'position' => EmployeePosition::AGENT,
            'nic_num' => '200012345678',
            'branch_id' => $testBranch->id,
            'is_active' => true,
        ]);

        // Testing only seeders
        Branch::factory(10)->create();
        Employee::factory(20)->create();
        Customer::factory(100)->create();
        SavingsAccount::factory(500)->create();

        // Savings transactions seeder now handles interest calculation at month ends
        $this->call(SavingsTransactionsSeeder::class);

        $this->call(FixedDepositsSeeder::class);

        // Calculate historical interest for fixed deposits
        $this->command->info('Calculating historical interest for fixed deposits...');
        $this->calculateHistoricalFdInterest();
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
