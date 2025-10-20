<?php

namespace App\Console\Commands;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\SavingsAccount;
use App\Models\SavingsAccountInterestCalculation;
use App\Models\SavingsTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreditSavingsInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:credit-savings {--month= : Month to credit (YYYY-MM format, defaults to last month)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit calculated interest to savings accounts by creating deposit transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting savings account interest crediting...');

        // Determine the calculation period
        if ($this->option('month')) {
            $periodStart = Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth();
        } else {
            // Default to last month
            $periodStart = Carbon::now()->subMonth()->startOfMonth();
        }

        $periodEnd = $periodStart->copy()->endOfMonth();

        $this->info("Crediting interest for period: {$periodStart->format('Y-m-d')} to {$periodEnd->format('Y-m-d')}");

        // Get all accounts with calculated interest in this period (piecewise calculations)
        $accountsWithInterest = SavingsAccountInterestCalculation::where('status', 'CALCULATED')
            ->where('calculation_period_start', '>=', $periodStart)
            ->where('calculation_period_end', '<=', $periodEnd)
            ->select('account_id')
            ->distinct()
            ->pluck('account_id');

        if ($accountsWithInterest->isEmpty()) {
            $this->warn('No calculated interest found to credit. Run interest:calculate-savings first.');
            return Command::SUCCESS;
        }

        $this->info("Found {$accountsWithInterest->count()} accounts with interest to credit");

        $credited = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($accountsWithInterest as $accountId) {
                $account = SavingsAccount::find($accountId);

                if (!$account) {
                    $this->error("Account not found for ID {$accountId}");
                    $errors++;
                    continue;
                }

                // Sum all piecewise interest calculations for this account in this period
                $totalInterest = SavingsAccountInterestCalculation::where('account_id', $accountId)
                    ->where('status', 'CALCULATED')
                    ->where('calculation_period_start', '>=', $periodStart)
                    ->where('calculation_period_end', '<=', $periodEnd)
                    ->sum('interest_amount');

                if ($totalInterest <= 0) {
                    $this->warn("Skipping account {$account->account_number} - zero or negative interest");
                    continue;
                }

                // Get balance before transaction
                $balanceBefore = $account->balance;

                // Create interest credit transaction (DEPOSIT type)
                $transaction = SavingsTransaction::create([
                    'type' => TransactionTypeEnum::DEPOSIT,
                    'to_id' => $account->id,
                    'from_id' => null,
                    'amount' => round($totalInterest, 2),
                    'status' => TransactionStatusEnum::COMPLETED,
                    'description' => "Monthly interest credit for {$periodStart->format('M Y')}",
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore + round($totalInterest, 2),
                ]);

                // Update all interest calculation records for this account to CREDITED
                SavingsAccountInterestCalculation::where('account_id', $accountId)
                    ->where('status', 'CALCULATED')
                    ->where('calculation_period_start', '>=', $periodStart)
                    ->where('calculation_period_end', '<=', $periodEnd)
                    ->update([
                        'status' => 'CREDITED',
                        'credited_date' => Carbon::now(),
                        'transaction_id' => $transaction->id,
                    ]);

                $credited++;
                $this->line("✓ Credited LKR " . round($totalInterest, 2) . " to {$account->account_number}");
            }

            DB::commit();

            $this->info("\n=== Interest Crediting Complete ===");
            $this->info("Accounts credited: {$credited}");
            if ($errors > 0) {
                $this->warn("Errors: {$errors}");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error crediting interest: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
