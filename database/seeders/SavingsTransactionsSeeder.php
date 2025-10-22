<?php

namespace Database\Seeders;

use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Models\SavingsAccountInterestCalculation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavingsTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eager load account type for interest rate
        $accounts = SavingsAccount::with('accountType')->where('status', 'ACTIVE')->get();

        if ($accounts->isEmpty()) {
            $this->command->warn('No active savings accounts found. Please seed savings accounts first.');
            return;
        }

        $this->command->info("Creating chronological transactions for {$accounts->count()} accounts...");

        // Define the time period: last 12 months
        // FIX: Corrected date order - start date should be 12 months ago, end date should be yesterday
        $startDate = now()->subMonths(12)->startOfMonth();
        $endDate = now()->subDay()->endOfDay(); // Go up to yesterday

        $this->command->info("Transaction period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        // Reset all account balances to minimum.
        // We assume a trigger *doesn't* fire for this, so we do it manually.
        $accountData = [];
        foreach ($accounts as $account) {
            $minBalance = $account->accountType->min_balance ?? 1000.00;
            $account->update(['balance' => $minBalance]);
            $accountData[$account->id] = [
                'last_calculation_date' => $startDate->copy(),
                'last_balance' => $minBalance, // Our local tracker for interest
            ];
        }

        // Generate transactions month by month
        $currentMonth = $startDate->copy();

        while ($currentMonth->lte($endDate)) {
            $monthStart = $currentMonth->copy()->startOfMonth();
            $monthEnd = $currentMonth->copy()->endOfMonth();

            if ($monthEnd->gt($endDate)) {
                $monthEnd = $endDate->copy();
            }

            $this->command->info("\nProcessing month: {$monthStart->format('M Y')}");

            // Collect all transactions for this month
            $monthTransactionData = [];

            foreach ($accounts as $account) {
                $transactionCount = rand(3, 10);

                for ($i = 0; $i < $transactionCount; $i++) {
                    // FIX: Cast diffInDays to int to avoid decimal values
                    $daysInRange = (int) $monthStart->diffInDays($monthEnd);
                    $transactionDate = $monthStart->copy()->addDays(rand(0, $daysInRange))
                        ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));
                    
                    if ($transactionDate->gt($endDate)) {
                        $transactionDate = $endDate->copy();
                    }

                    $rand = rand(1, 100);

                    if ($rand <= 60) { // DEPOSIT
                        $monthTransactionData[] = [
                            'account_id' => $account->id, 'type' => 'DEPOSIT', 'from_id' => null, 'to_id' => $account->id,
                            'amount' => rand(100, 5000), 'description' => $this->getRandomDepositDescription(),
                            'transaction_date' => $transactionDate,
                        ];
                    } elseif ($rand <= 90) { // WITHDRAWAL
                        $monthTransactionData[] = [
                            'account_id' => $account->id, 'type' => 'WITHDRAWAL', 'from_id' => $account->id, 'to_id' => null,
                            'amount' => rand(100, 3000), 'description' => $this->getRandomWithdrawalDescription(),
                            'transaction_date' => $transactionDate,
                        ];
                    } else { // TRANSFER
                        $toAccount = $accounts->where('id', '!=', $account->id)->random();
                        if ($toAccount) {
                            $monthTransactionData[] = [
                                'account_id' => $account->id, 'type' => 'TRANSFER', 'from_id' => $account->id, 'to_id' => $toAccount->id,
                                'amount' => rand(100, 2000), 'description' => 'Transfer to account ' . $toAccount->account_number,
                                'transaction_date' => $transactionDate,
                            ];
                        }
                    }
                }
            }

            // Sort transactions chronologically
            usort($monthTransactionData, fn ($a, $b) => $a['transaction_date'] <=> $b['transaction_date']);

            $this->command->info("  Creating " . count($monthTransactionData) . " transactions chronologically...");

            $createdCount = 0;
            $interestCalculations = 0;

            foreach ($monthTransactionData as $txnData) {
                $account = $accounts->firstWhere('id', $txnData['account_id']);
                
                // --- [CRITICAL CHANGE 1] ---
                // Refresh the model to get the *actual* balance updated by the trigger
                // from the *previous* transaction.
                $account->refresh();
                $currentBalance = $account->balance;

                // Validate withdrawals and transfers
                if (in_array($txnData['type'], ['WITHDRAWAL', 'TRANSFER'])) {
                    $minBalance = $account->accountType->min_balance ?? 1000;
                    $maxAmount = $currentBalance - $minBalance;

                    if ($maxAmount < 100) {
                        continue; // Can't withdraw, skip
                    }
                    if ($txnData['amount'] > $maxAmount) {
                        $txnData['amount'] = floor($maxAmount);
                    }
                }

                // Predict balances for the transaction log
                $balanceBefore = $currentBalance;
                $balanceAfter = $currentBalance;
                if ($txnData['type'] == 'DEPOSIT') {
                    $balanceAfter = $currentBalance + $txnData['amount'];
                } elseif (in_array($txnData['type'], ['WITHDRAWAL', 'TRANSFER'])) {
                    $balanceAfter = $currentBalance - $txnData['amount'];
                }

                // --- INTEREST CALCULATION (Happens *before* the transaction) ---
                // Calculate interest on the balance held *since the last event*
                if ($this->calculateInterestForTransaction($account, $accountData[$account->id], $txnData['transaction_date'])) {
                    $interestCalculations++;
                }
                
                // --- TRANSACTION CREATION ---
                // The database trigger will fire *here* and update $account->balance
                SavingsTransaction::create([
                    'type' => $txnData['type'],
                    'from_id' => $txnData['from_id'],
                    'to_id' => $txnData['to_id'],
                    'amount' => $txnData['amount'],
                    'status' => 'COMPLETED',
                    'description' => $txnData['description'],
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'created_at' => $txnData['transaction_date'],
                    'updated_at' => $txnData['transaction_date'],
                ]);

                $createdCount++;

                // --- [CRITICAL CHANGE 2] ---
                // We do NOT call $account->update().
                // Instead, we update our *local tracker* to reflect the new state.
                $accountData[$account->id]['last_calculation_date'] = $txnData['transaction_date'];
                $accountData[$account->id]['last_balance'] = $balanceAfter; // Use predicted balance
            }

            $this->command->info("  Created {$createdCount} transactions");
            $this->command->info("  Created {$interestCalculations} interest calculations");

            // At month end, calculate interest from last transaction to month end
            $this->command->info("  Calculating interest to month end...");
            $monthEndCalculations = 0;

            foreach ($accounts as $account) {
                // We pass the *tracker data* which has the last-known balance
                if ($this->calculateInterestToMonthEnd($account, $accountData[$account->id], $monthEnd)) {
                    $monthEndCalculations++;
                }
                // Update tracking for next month
                $accountData[$account->id]['last_calculation_date'] = $monthEnd->copy()->addDay()->startOfDay();
            }

            $this->command->info("  Created {$monthEndCalculations} month-end interest calculations");

            // Credit all interest for this month (but not for current month)
            if ($monthEnd->lt(now()->startOfMonth())) {
                $this->command->info("  Crediting interest for {$monthStart->format('M Y')}...");
                $creditedCount = $this->creditMonthInterest($monthStart, $monthEnd);
                $this->command->info("  ✓ Credited interest to {$creditedCount} accounts");
            }

            $currentMonth->addMonth();
        }

        $this->command->info("\n=== Seeding Complete ===");
    }

    /**
     * Calculate interest from last calculation date to transaction date
     */
    private function calculateInterestForTransaction(SavingsAccount $account, array &$accountData, Carbon $transactionDate): bool
    {
        $lastCalcDate = $accountData['last_calculation_date'];
        $balance = $accountData['last_balance']; // Use the tracked balance

        // FIX: Cast to int to ensure whole number of days
        $days = (int) $lastCalcDate->diffInDays($transactionDate);

        if ($days <= 0 || $balance <= 0) {
            return false;
        }

        $interestRate = $account->accountType->interest_rate;
        $dailyRate = $interestRate / 365;
        $interest = $balance * $dailyRate * $days;

        SavingsAccountInterestCalculation::create([
            'account_id' => $account->id,
            'calculation_period_start' => $lastCalcDate,
            'calculation_period_end' => $transactionDate->copy()->subDay()->endOfDay(),
            'principal_amount' => $balance,
            'interest_rate' => $interestRate,
            'days_calculated' => $days,
            'interest_amount' => $interest, // Store with precision
            'status' => 'CALCULATED',
            'calculation_date' => $transactionDate,
        ]);

        return true;
    }

    /**
     * Calculate interest from last transaction to month end
     */
    private function calculateInterestToMonthEnd(SavingsAccount $account, array &$accountData, Carbon $monthEnd): bool
    {
        $lastCalcDate = $accountData['last_calculation_date'];
        
        // --- [CRITICAL CHANGE 3] ---
        // We must refresh the account model to get the *true* final balance
        // that the triggers have set.
        $account->refresh();
        $balance = $account->balance;

        // Update the tracker's balance to match, so it's correct for the credit.
        $accountData['last_balance'] = $balance;

        // FIX: Cast to int to ensure whole number of days, and add 1 for inclusive range
        $days = (int) $lastCalcDate->diffInDays($monthEnd) + 1;

        if ($days <= 0 || $balance <= 0) {
            return false;
        }

        $interestRate = $account->accountType->interest_rate;
        $dailyRate = $interestRate / 365;
        $interest = $balance * $dailyRate * $days;

        SavingsAccountInterestCalculation::create([
            'account_id' => $account->id,
            'calculation_period_start' => $lastCalcDate,
            'calculation_period_end' => $monthEnd,
            'principal_amount' => $balance,
            'interest_rate' => $interestRate,
            'days_calculated' => $days,
            'interest_amount' => $interest,
            'status' => 'CALCULATED',
            'calculation_date' => $monthEnd,
        ]);

        return true;
    }

    /**
     * Credit all interest calculations for a month
     */
    private function creditMonthInterest(Carbon $monthStart, Carbon $monthEnd): int
    {
        DB::beginTransaction();
        try {
            $calculations = SavingsAccountInterestCalculation::where('status', 'CALCULATED')
                ->whereBetween('calculation_period_end', [$monthStart, $monthEnd])
                ->get();

            $groupedCalculations = $calculations->groupBy('account_id');
            $accountIds = $groupedCalculations->keys();
            
            // Lock accounts for the update
            $accounts = SavingsAccount::whereIn('id', $accountIds)->lockForUpdate()->get()->keyBy('id');

            $creditedCount = 0;
            $creditDate = $monthEnd->copy()->endOfDay();

            foreach ($groupedCalculations as $accountId => $accountCalculations) {
                $account = $accounts->get($accountId);
                if (!$account) continue;

                $totalInterest = $accountCalculations->sum('interest_amount');
                $totalInterest = round($totalInterest, 2); // Round *only* at the end

                if ($totalInterest <= 0) continue;

                // We must read the balance *right before* creating the transaction
                $currentBalance = $account->balance; 
                $newBalance = $currentBalance + $totalInterest;

                $transaction = SavingsTransaction::create([
                    'type' => 'DEPOSIT',
                    'from_id' => null,
                    'to_id' => $account->id,
                    'amount' => $totalInterest,
                    'status' => 'COMPLETED',
                    'description' => 'Monthly interest credit for ' . $monthStart->format('M Y'),
                    'balance_before' => $currentBalance,
                    'balance_after' => $newBalance,
                    'created_at' => $creditDate,
                    'updated_at' => $creditDate,
                ]);
                
                // --- [CRITICAL CHANGE 4] ---
                // We DO NOT update the balance. The trigger associated with the
                // `SavingsTransaction::create` call above has already handled it.

                // We only update the calculation statuses
                $calculationIds = $accountCalculations->pluck('id');
                SavingsAccountInterestCalculation::whereIn('id', $calculationIds)->update([
                    'status' => 'CREDITED',
                    'credited_date' => $creditDate,
                    'transaction_id' => $transaction->id,
                ]);

                $creditedCount++;
            }

            DB::commit();
            return $creditedCount;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error crediting interest: " . $e->getMessage());
            return 0;
        }
    }

    private function getRandomDepositDescription(): string
    {
        $descriptions = [
            'Cash deposit', 'Salary credit', 'Cheque deposit', 'Online transfer received',
            'Bonus payment', 'Freelance payment', 'Refund credit', 'Gift deposit',
            'Dividend payment', 'Business income',
        ];
        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomWithdrawalDescription(): string
    {
        $descriptions = [
            'ATM withdrawal', 'Cash withdrawal', 'Bill payment', 'Shopping payment',
            'Utility payment', 'Rent payment', 'Medical expenses', 'Online purchase',
            'Subscription payment', 'Insurance premium',
        ];
        return $descriptions[array_rand($descriptions)];
    }
}