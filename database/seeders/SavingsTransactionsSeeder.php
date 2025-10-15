<?php

namespace Database\Seeders;

use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use Illuminate\Database\Seeder;

class SavingsTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = SavingsAccount::where('status', 'ACTIVE')->get();

        if ($accounts->isEmpty()) {
            $this->command->warn('No active savings accounts found. Please seed savings accounts first.');
            return;
        }

        $this->command->info("Creating transactions for {$accounts->count()} accounts...");
        $this->command->info("Note: Account balances will be automatically updated by database trigger");

        foreach ($accounts as $account) {
            // Reset account balance to minimum (trigger will update it)
            $account->update(['balance' => $account->accountType->min_balance ?? 1000.00]);

            // Create 10-30 historical transactions per account
            $transactionCount = rand(10, 30);

            for ($i = $transactionCount; $i >= 1; $i--) {
                // Generate transaction date (going backwards in time)
                $transactionDate = now()->subDays(rand($i * 3, $i * 7));

                // Refresh account to get current balance (updated by trigger)
                $account->refresh();
                $currentBalance = $account->balance;

                // Determine transaction type (60% deposits, 30% withdrawals, 10% transfers)
                $rand = rand(1, 100);

                if ($rand <= 60) {
                    // DEPOSIT
                    $amount = rand(100, 5000);
                    $balanceBefore = $currentBalance;
                    $balanceAfter = $currentBalance + $amount; // For display only, trigger handles actual update

                    SavingsTransaction::create([
                        'type' => 'DEPOSIT',
                        'from_id' => null,
                        'to_id' => $account->id,
                        'amount' => $amount,
                        'status' => 'COMPLETED',
                        'description' => $this->getRandomDepositDescription(),
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ]);

                } elseif ($rand <= 90) {
                    // WITHDRAWAL (only if sufficient balance)
                    $maxWithdrawal = min($currentBalance - ($account->accountType->min_balance ?? 1000), 3000);

                    if ($maxWithdrawal > 100) {
                        $amount = rand(100, $maxWithdrawal);
                        $balanceBefore = $currentBalance;
                        $balanceAfter = $currentBalance - $amount;

                        SavingsTransaction::create([
                            'type' => 'WITHDRAWAL',
                            'from_id' => $account->id,
                            'to_id' => null,
                            'amount' => $amount,
                            'status' => 'COMPLETED',
                            'description' => $this->getRandomWithdrawalDescription(),
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceAfter,
                            'created_at' => $transactionDate,
                            'updated_at' => $transactionDate,
                        ]);
                    }

                } else {
                    // TRANSFER (to another random account)
                    $toAccount = $accounts->where('id', '!=', $account->id)->random();
                    $maxTransfer = min($currentBalance - ($account->accountType->min_balance ?? 1000), 2000);

                    if ($maxTransfer > 100 && $toAccount) {
                        $amount = rand(100, $maxTransfer);
                        $balanceBefore = $currentBalance;
                        $balanceAfter = $currentBalance - $amount;

                        SavingsTransaction::create([
                            'type' => 'TRANSFER',
                            'from_id' => $account->id,
                            'to_id' => $toAccount->id,
                            'amount' => $amount,
                            'status' => 'COMPLETED',
                            'description' => 'Transfer to account ' . $toAccount->account_number,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceAfter,
                            'created_at' => $transactionDate,
                            'updated_at' => $transactionDate,
                        ]);
                    }
                }
            }

            // Refresh to get final balance after trigger updates
            $account->refresh();
            $this->command->info("Created {$transactionCount} transactions for account {$account->account_number} | Final balance: Rs. " . number_format($account->balance, 2));
        }

        $totalTransactions = SavingsTransaction::count();
        $depositCount = SavingsTransaction::where('type', 'DEPOSIT')->count();
        $withdrawalCount = SavingsTransaction::where('type', 'WITHDRAWAL')->count();
        $transferCount = SavingsTransaction::where('type', 'TRANSFER')->count();

        $this->command->info("✓ Successfully created {$totalTransactions} transactions");
        $this->command->info("  - Deposits: {$depositCount}");
        $this->command->info("  - Withdrawals: {$withdrawalCount}");
        $this->command->info("  - Transfers: {$transferCount}");
    }

    private function getRandomDepositDescription(): string
    {
        $descriptions = [
            'Cash deposit',
            'Salary credit',
            'Cheque deposit',
            'Online transfer received',
            'Bonus payment',
            'Freelance payment',
            'Refund credit',
            'Gift deposit',
            'Dividend payment',
            'Interest earned',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomWithdrawalDescription(): string
    {
        $descriptions = [
            'ATM withdrawal',
            'Cash withdrawal',
            'Bill payment',
            'Shopping payment',
            'Utility payment',
            'Rent payment',
            'Medical expenses',
            'Online purchase',
            'Subscription payment',
            'Insurance premium',
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
