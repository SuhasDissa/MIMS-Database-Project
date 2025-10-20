<?php

namespace App\Console\Commands;

use App\Models\FixedDeposit;
use App\Models\FdInterestCalculation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateFdInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:calculate-fd {--month= : Month to calculate (YYYY-MM format, defaults to last month)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate monthly interest for all active fixed deposits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting fixed deposit interest calculation...');

        // Determine the calculation period
        if ($this->option('month')) {
            $periodStart = Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth();
        } else {
            // Default to last month
            $periodStart = Carbon::now()->subMonth()->startOfMonth();
        }

        $periodEnd = $periodStart->copy()->endOfMonth();

        $this->info("Calculating interest for period: {$periodStart->format('Y-m-d')} to {$periodEnd->format('Y-m-d')}");

        // Get all active FDs that were active during the period
        $fixedDeposits = FixedDeposit::with('fdType')
            ->where('status', 'ACTIVE')
            ->whereDate('start_date', '<=', $periodEnd)
            ->where(function($query) use ($periodStart) {
                $query->whereNull('maturity_date')
                    ->orWhereDate('maturity_date', '>=', $periodStart);
            })
            ->get();

        $this->info("Found {$fixedDeposits->count()} active fixed deposits");

        $calculationsCreated = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($fixedDeposits as $fd) {
                // Check if interest already calculated for this period
                $existingCalculation = FdInterestCalculation::where('account_id', $fd->id)
                    ->where('calculation_period_start', $periodStart)
                    ->where('calculation_period_end', $periodEnd)
                    ->first();

                if ($existingCalculation) {
                    $this->warn("Interest already calculated for FD {$fd->fd_number}");
                    continue;
                }

                // Get the FD type and interest rate
                $fdType = $fd->fdType;
                if (!$fdType) {
                    $this->error("FD {$fd->fd_number} has no FD type");
                    $errors++;
                    continue;
                }

                $interestRate = $fdType->interest_rate;
                $principalAmount = $fd->principal_amount;

                // Calculate days in period (30 days as per requirement)
                $daysCalculated = 30;

                // Calculate interest: (Principal × Rate × 30 days) / 365
                $interestAmount = ($principalAmount * $interestRate * $daysCalculated) / 365;
                $interestAmount = round($interestAmount, 2);

                // Create interest calculation record
                FdInterestCalculation::create([
                    'account_id' => $fd->id,
                    'calculation_period_start' => $periodStart,
                    'calculation_period_end' => $periodEnd,
                    'principal_amount' => $principalAmount,
                    'interest_rate' => $interestRate,
                    'days_calculated' => $daysCalculated,
                    'interest_amount' => $interestAmount,
                    'status' => 'CALCULATED',
                    'calculation_date' => Carbon::now(),
                ]);

                $calculationsCreated++;
                $this->line("✓ Calculated interest for FD {$fd->fd_number}: LKR {$interestAmount}");
            }

            DB::commit();

            $this->info("\n=== FD Interest Calculation Complete ===");
            $this->info("Calculations created: {$calculationsCreated}");
            if ($errors > 0) {
                $this->warn("Errors: {$errors}");
            }
            $this->info("Run 'interest:credit-fd' to credit these amounts to linked savings accounts");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error calculating FD interest: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
