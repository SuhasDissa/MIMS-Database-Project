<?php

namespace App\Console\Commands;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\FixedDeposit;
use App\Models\FdInterestCalculation;
use App\Models\SavingsTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreditFdInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:credit-fd {--month= : Month to credit (YYYY-MM format, defaults to last month)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit calculated FD interest to linked savings accounts by creating deposit transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting FD interest crediting...');

        // Determine the calculation period
        if ($this->option('month')) {
            $periodStart = Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth();
        } else {
            // Default to last month
            $periodStart = Carbon::now()->subMonth()->startOfMonth();
        }

        $periodEnd = $periodStart->copy()->endOfMonth();

        $this->info("Crediting FD interest for period: {$periodStart->format('Y-m-d')} to {$periodEnd->format('Y-m-d')}");

        // Get all calculated but not credited interest entries
        $calculations = FdInterestCalculation::with('fixedDeposit.linkedSavingsAccount')
            ->where('status', 'CALCULATED')
            ->where('calculation_period_start', $periodStart)
            ->where('calculation_period_end', $periodEnd)
            ->get();

        if ($calculations->isEmpty()) {
            $this->warn('No calculated FD interest found to credit. Run interest:calculate-fd first.');
            return Command::SUCCESS;
        }

        $this->info("Found {$calculations->count()} FD interest calculations to credit");

        $credited = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($calculations as $calculation) {
                $fd = $calculation->fixedDeposit;

                if (!$fd) {
                    $this->error("FD not found for calculation ID {$calculation->id}");
                    $errors++;
                    continue;
                }

                // Get linked savings account
                $savingsAccount = $fd->linkedSavingsAccount;

                if (!$savingsAccount) {
                    $this->error("No linked savings account for FD {$fd->fd_number}");
                    $errors++;
                    continue;
                }

                if ($calculation->interest_amount <= 0) {
                    $this->warn("Skipping FD {$fd->fd_number} - zero or negative interest");
                    continue;
                }

                // Get balance before transaction
                $balanceBefore = $savingsAccount->balance;

                // Create interest credit transaction to savings account
                $transaction = SavingsTransaction::create([
                    'type' => TransactionTypeEnum::DEPOSIT,
                    'to_id' => $savingsAccount->id,
                    'from_id' => null,
                    'amount' => $calculation->interest_amount,
                    'status' => TransactionStatusEnum::COMPLETED,
                    'description' => "FD interest credit from {$fd->fd_number} for {$periodStart->format('M Y')}",
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore + $calculation->interest_amount,
                ]);

                // Update interest calculation record
                $calculation->update([
                    'status' => 'CREDITED',
                    'credited_date' => Carbon::now(),
                    'transaction_id' => $transaction->id,
                ]);

                $credited++;
                $this->line("✓ Credited LKR {$calculation->interest_amount} from FD {$fd->fd_number} to {$savingsAccount->account_number}");
            }

            DB::commit();

            $this->info("\n=== FD Interest Crediting Complete ===");
            $this->info("FDs credited: {$credited}");
            if ($errors > 0) {
                $this->warn("Errors: {$errors}");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error crediting FD interest: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
