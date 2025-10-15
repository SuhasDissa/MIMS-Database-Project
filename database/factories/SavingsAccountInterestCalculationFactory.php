<?php

namespace Database\Factories;

use App\Models\SavingsAccount;
use App\Models\SavingsAccountInterestCalculation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavingsAccountInterestCalculation>
 */
class SavingsAccountInterestCalculationFactory extends Factory
{
    protected $model = SavingsAccountInterestCalculation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a calculation period (typically monthly)
        $periodEnd = $this->faker->dateTimeBetween('-2 years', 'now');
        $periodStart = (clone $periodEnd)->modify('-1 month');

        // Calculate days
        $days = $periodStart->diff($periodEnd)->days;

        // Random principal amount (based on typical savings account balance)
        $principalAmount = $this->faker->randomFloat(2, 1000, 500000);

        // Interest rate (typical savings account rates: 3-8%)
        $interestRate = $this->faker->randomFloat(4, 3.0000, 8.0000);

        // Calculate interest: (Principal × Rate × Days) / (365 × 100)
        $interestAmount = ($principalAmount * $interestRate * $days) / (365 * 100);

        // Determine status - 80% credited, 20% calculated
        $status = $this->faker->randomElement(['CREDITED', 'CREDITED', 'CREDITED', 'CREDITED', 'CALCULATED']);

        $calculationDate = $this->faker->dateTimeBetween($periodEnd, 'now');
        $creditedDate = $status === 'CREDITED'
            ? $this->faker->dateTimeBetween($calculationDate, 'now')
            : null;

        return [
            'account_id' => SavingsAccount::factory(),
            'calculation_period_start' => $periodStart,
            'calculation_period_end' => $periodEnd,
            'principal_amount' => $principalAmount,
            'interest_rate' => $interestRate,
            'days_calculated' => $days,
            'interest_amount' => round($interestAmount, 2),
            'status' => $status,
            'calculation_date' => $calculationDate,
            'credited_date' => $creditedDate,
            'transaction_id' => null, // Will be linked when transactions are created
        ];
    }

    /**
     * Indicate that the interest calculation is credited.
     */
    public function credited(): static
    {
        return $this->state(function (array $attributes) {
            $calculationDate = $attributes['calculation_date'] ?? now();

            return [
                'status' => 'CREDITED',
                'credited_date' => $this->faker->dateTimeBetween($calculationDate, 'now'),
            ];
        });
    }

    /**
     * Indicate that the interest calculation is only calculated (not yet credited).
     */
    public function calculated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'CALCULATED',
            'credited_date' => null,
        ]);
    }

    /**
     * Indicate that the interest calculation failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'FAILED',
            'credited_date' => null,
        ]);
    }

    /**
     * Create interest calculation for a specific account.
     */
    public function forAccount(int $accountId): static
    {
        return $this->state(fn (array $attributes) => [
            'account_id' => $accountId,
        ]);
    }

    /**
     * Create interest calculation for a specific period.
     */
    public function forPeriod(\DateTime $start, \DateTime $end): static
    {
        $days = $start->diff($end)->days;

        return $this->state(function (array $attributes) use ($start, $end, $days) {
            $interestAmount = ($attributes['principal_amount'] * $attributes['interest_rate'] * $days) / (365 * 100);

            return [
                'calculation_period_start' => $start,
                'calculation_period_end' => $end,
                'days_calculated' => $days,
                'interest_amount' => round($interestAmount, 2),
            ];
        });
    }
}
