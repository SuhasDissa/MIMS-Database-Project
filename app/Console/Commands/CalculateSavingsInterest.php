<?php

namespace App\Console\Commands;

use App\Models\SavingsAccount;
use App\Models\SavingsAccountInterestCalculation;
use App\Models\SavingsTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateSavingsInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:calculate-savings {--month= : Month to calculate (YYYY-MM format, defaults to last month)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate monthly interest for all active savings accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting piecewise savings account interest calculation...');

        // Determine the calculation period
        if ($this->option('month')) {
            $periodStart = Carbon::createFromFormat('Y-m', $this->option('month'))->startOfMonth();
        } else {
            // Default to last month
            $periodStart = Carbon::now()->subMonth()->startOfMonth();
        }

        $periodEnd = $periodStart->copy()->endOfMonth();

        $this->info("Calculating interest for period: {$periodStart->format('Y-m-d')} to {$periodEnd->format('Y-m-d')}");

        // Get all active savings accounts with their account types
        $accounts = SavingsAccount::with('accountType')
            ->where('status', 'ACTIVE')
            ->whereDate('opened_date', '<=', $periodEnd)
            ->get();

        $this->info("Found {$accounts->count()} active accounts");

        $calculationsCreated = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            foreach ($accounts as $account) {
                // Get the account type and interest rate
                $accountType = $account->accountType;
                if (!$accountType) {
                    $this->error("Account {$account->account_number} has no account type");
                    $errors++;
                    continue;
                }

                $interestRate = $accountType->interest_rate;

                // Find the last interest calculation date for this account
                $lastInterestCalc = SavingsAccountInterestCalculation::where('account_id', $account->id)
                    ->where('calculation_period_end', '<', $periodStart)
                    ->orderBy('calculation_period_end', 'desc')
                    ->first();

                // Starting point is either the last interest calc date or account opening date
                $startingPoint = $lastInterestCalc
                    ? Carbon::parse($lastInterestCalc->calculation_period_end)->addDay()
                    : Carbon::parse($account->opened_date);

                // Make sure we don't start before the period
                if ($startingPoint->lt($periodStart)) {
                    $startingPoint = $periodStart->copy();
                }

                // Get all transactions for this account in the period, ordered by date
                $transactions = SavingsTransaction::where(function($query) use ($account) {
                        $query->where('from_id', $account->id)
                              ->orWhere('to_id', $account->id);
                    })
                    ->whereBetween('created_at', [$periodStart, $periodEnd])
                    ->orderBy('created_at')
                    ->get();

                // Calculate opening balance for the period
                $openingBalance = $this->getBalanceAtDate($account, $startingPoint);

                $currentBalance = $openingBalance;
                $currentDate = $startingPoint->copy();
                $accountCalculations = 0;

                // If no transactions in period, create one calculation for the whole period
                if ($transactions->isEmpty()) {
                    $days = $currentDate->diffInDays($periodEnd) + 1;

                    if ($days > 0 && $currentBalance > 0) {
                        $interest = ($currentBalance * $interestRate * $days) / 365;

                        SavingsAccountInterestCalculation::create([
                            'account_id' => $account->id,
                            'calculation_period_start' => $currentDate,
                            'calculation_period_end' => $periodEnd,
                            'principal_amount' => $currentBalance,
                            'interest_rate' => $interestRate,
                            'days_calculated' => $days,
                            'interest_amount' => round($interest, 2),
                            'status' => 'CALCULATED',
                            'calculation_date' => Carbon::now(),
                        ]);

                        $accountCalculations++;
                    }
                } else {
                    // Calculate interest piecewise between transactions
                    foreach ($transactions as $transaction) {
                        $transactionDate = Carbon::parse($transaction->created_at);

                        // Calculate interest from current date to transaction date
                        $days = $currentDate->diffInDays($transactionDate);

                        if ($days > 0 && $currentBalance > 0) {
                            $interest = ($currentBalance * $interestRate * $days) / 365;

                            SavingsAccountInterestCalculation::create([
                                'account_id' => $account->id,
                                'calculation_period_start' => $currentDate,
                                'calculation_period_end' => $transactionDate->copy()->subDay(),
                                'principal_amount' => $currentBalance,
                                'interest_rate' => $interestRate,
                                'days_calculated' => $days,
                                'interest_amount' => round($interest, 2),
                                'status' => 'CALCULATED',
                                'calculation_date' => Carbon::now(),
                            ]);

                            $accountCalculations++;
                        }

                        // Update balance based on transaction
                        if ($transaction->to_id == $account->id) {
                            $currentBalance += $transaction->amount;
                        }
                        if ($transaction->from_id == $account->id) {
                            $currentBalance -= $transaction->amount;
                        }

                        // Move current date forward
                        $currentDate = $transactionDate->copy();
                    }

                    // Calculate interest from last transaction to end of period
                    $days = $currentDate->diffInDays($periodEnd) + 1;

                    if ($days > 0 && $currentBalance > 0) {
                        $interest = ($currentBalance * $interestRate * $days) / 365;

                        SavingsAccountInterestCalculation::create([
                            'account_id' => $account->id,
                            'calculation_period_start' => $currentDate,
                            'calculation_period_end' => $periodEnd,
                            'principal_amount' => $currentBalance,
                            'interest_rate' => $interestRate,
                            'days_calculated' => $days,
                            'interest_amount' => round($interest, 2),
                            'status' => 'CALCULATED',
                            'calculation_date' => Carbon::now(),
                        ]);

                        $accountCalculations++;
                    }
                }

                $calculationsCreated += $accountCalculations;

                if ($accountCalculations > 0) {
                    $totalInterest = SavingsAccountInterestCalculation::where('account_id', $account->id)
                        ->where('calculation_period_start', '>=', $periodStart)
                        ->where('calculation_period_end', '<=', $periodEnd)
                        ->sum('interest_amount');

                    $this->line("✓ {$account->account_number}: {$accountCalculations} calculations, Total Interest: LKR {$totalInterest}");
                }
            }

            DB::commit();

            $this->info("\n=== Interest Calculation Complete ===");
            $this->info("Piecewise calculations created: {$calculationsCreated}");
            if ($errors > 0) {
                $this->warn("Errors: {$errors}");
            }
            $this->info("Run 'interest:credit-savings' to credit these amounts to accounts");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error calculating interest: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    /**
     * Get account balance at a specific date by reconstructing from transactions
     */
    private function getBalanceAtDate(SavingsAccount $account, Carbon $date): float
    {
        // Get all transactions up to the date
        $credits = SavingsTransaction::where('to_id', $account->id)
            ->where('created_at', '<', $date)
            ->sum('amount');

        $debits = SavingsTransaction::where('from_id', $account->id)
            ->where('created_at', '<', $date)
            ->sum('amount');

        return $credits - $debits;
    }
}
