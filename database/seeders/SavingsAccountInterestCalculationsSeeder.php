<?php

namespace Database\Seeders;

use App\Models\SavingsAccount;
use App\Models\SavingsAccountInterestCalculation;
use App\Models\SavingsTransaction;
use Illuminate\Database\Seeder;

class SavingsAccountInterestCalculationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing active savings accounts
        $accounts = SavingsAccount::where('status', 'ACTIVE')->get();

        if ($accounts->isEmpty()) {
            $this->command->warn('No active savings accounts found. Please seed savings accounts first.');
            return;
        }

        $this->command->info("Creating interest calculations for {$accounts->count()} accounts...");

        foreach ($accounts as $account) {
            // Get the account's interest rate from its account type
            $interestRate = $account->accountType->interest_rate ?? 5.00;

            // Create 3-12 historical interest calculations per account (monthly calculations)
            $calculationCount = rand(3, 12);

            for ($i = $calculationCount; $i >= 1; $i--) {
                // Calculate period (going backwards from now, monthly periods)
                $periodEnd = now()->subMonths($i)->endOfMonth();
                $periodStart = (clone $periodEnd)->startOfMonth();

                // Use the account's balance as principal (with some variation for historical data)
                $principalAmount = $account->balance * rand(80, 120) / 100;

                // Calculate days in period
                $days = $periodStart->diffInDays($periodEnd);

                // Calculate interest: (Principal × Rate × Days) / (365 × 100)
                $interestAmount = ($principalAmount * $interestRate * $days) / (365 * 100);

                // Most recent 2 calculations might be "CALCULATED" (not yet credited)
                // Others should be "CREDITED"
                $status = ($i <= 2 && rand(1, 100) > 70) ? 'CALCULATED' : 'CREDITED';

                $calculationDate = $periodEnd->copy()->addDays(rand(1, 5));
                $creditedDate = $status === 'CREDITED'
                    ? $calculationDate->copy()->addDays(rand(1, 3))
                    : null;

                $transactionId = null;

                // Create a transaction if the interest was credited
                if ($status === 'CREDITED') {
                    $balanceBefore = $account->balance;
                    $balanceAfter = $account->balance + round($interestAmount, 2);

                    $transaction = SavingsTransaction::create([
                        'type' => 'DEPOSIT',
                        'from_id' => null, // Interest credit has no source account
                        'to_id' => $account->id,
                        'amount' => round($interestAmount, 2),
                        'status' => 'COMPLETED',
                        'description' => 'Interest credit for period ' . $periodStart->format('M Y'),
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'created_at' => $creditedDate,
                        'updated_at' => $creditedDate,
                    ]);

                    $transactionId = $transaction->id;

                    // Trigger will automatically update account balance
                    $account->refresh();
                }

                SavingsAccountInterestCalculation::create([
                    'account_id' => $account->id,
                    'calculation_period_start' => $periodStart,
                    'calculation_period_end' => $periodEnd,
                    'principal_amount' => round($principalAmount, 2),
                    'interest_rate' => $interestRate,
                    'days_calculated' => $days,
                    'interest_amount' => round($interestAmount, 2),
                    'status' => $status,
                    'calculation_date' => $calculationDate,
                    'credited_date' => $creditedDate,
                    'transaction_id' => $transactionId,
                ]);
            }

            $this->command->info("Created {$calculationCount} interest calculations for account {$account->account_number}");
        }

        $totalCalculations = SavingsAccountInterestCalculation::count();
        $creditedCount = SavingsAccountInterestCalculation::where('status', 'CREDITED')->count();
        $calculatedCount = SavingsAccountInterestCalculation::where('status', 'CALCULATED')->count();
        $totalTransactions = SavingsTransaction::where('description', 'like', 'Interest credit%')->count();

        $this->command->info("✓ Successfully created {$totalCalculations} interest calculations");
        $this->command->info("  - Credited: {$creditedCount}");
        $this->command->info("  - Calculated (pending): {$calculatedCount}");
        $this->command->info("  - Interest transactions created: {$totalTransactions}");
    }
}
