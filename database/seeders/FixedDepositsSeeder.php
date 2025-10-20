<?php

namespace Database\Seeders;

use App\Enums\FixedDepositStatusEnum;
use App\Enums\InterestFrequencyEnum;
use App\Enums\InterestPayoutOptionEnum;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\FixedDeposit;
use App\Models\FixedDepositType;
use App\Models\SavingsAccount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FixedDepositsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Fixed Deposits...');

        // Get all FD types
        $fdTypes = FixedDepositType::all();

        if ($fdTypes->isEmpty()) {
            $this->command->error('No FD types found. Please run FixedDepositTypesSeeder first.');
            return;
        }

        // Get active savings accounts with balance > 5000
        $savingsAccounts = SavingsAccount::where('status', 'ACTIVE')
            ->where('balance', '>', 5000)
            ->inRandomOrder()
            ->limit(15)
            ->get();

        if ($savingsAccounts->isEmpty()) {
            $this->command->error('No suitable savings accounts found for FDs.');
            return;
        }

        $fdsCreated = 0;

        foreach ($savingsAccounts->take(10) as $savingsAccount) {
            // Get the first customer associated with this savings account
            $customer = $savingsAccount->customers()->first();

            if (!$customer) {
                $this->command->warn("Savings account {$savingsAccount->account_number} has no customers, skipping...");
                continue;
            }

            // Randomly select an FD type
            $fdType = $fdTypes->random();

            // Calculate start date (between 1-6 months ago)
            $monthsAgo = rand(1, 6);
            $startDate = Carbon::now()->subMonths($monthsAgo)->startOfMonth();

            // Calculate maturity date
            $maturityDate = $startDate->copy()->addMonths($fdType->tenure_months);

            // Principal amount between 10,000 and 100,000
            $principalAmount = rand(10000, 100000);

            // Calculate maturity amount
            $interestAmount = ($principalAmount * $fdType->interest_rate * $fdType->tenure_months) / 12;
            $maturityAmount = $principalAmount + $interestAmount;

            // Generate FD number
            $fdNumber = 'FD' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);

            // Create the Fixed Deposit
            FixedDeposit::create([
                'fd_number' => $fdNumber,
                'customer_id' => $customer->id,
                'fd_type_id' => $fdType->id,
                'branch_id' => $savingsAccount->branch_id,
                'linked_account_id' => $savingsAccount->id,
                'principal_amount' => $principalAmount,
                'interest_freq' => InterestFrequencyEnum::MONTHLY,
                'maturity_amount' => round($maturityAmount, 2),
                'start_date' => $startDate,
                'maturity_date' => $maturityDate,
                'status' => FixedDepositStatusEnum::ACTIVE,
                'interest_payout_option' => InterestPayoutOptionEnum::TRANSFER_TO_SAVINGS,
                'auto_renewal' => false,
            ]);

            $fdsCreated++;
            $this->command->line("Created FD {$fdNumber} - {$fdType->name} - LKR {$principalAmount} - Customer: {$customer->first_name} {$customer->last_name}");
        }

        $this->command->info("✓ Created {$fdsCreated} Fixed Deposits");
    }
}
