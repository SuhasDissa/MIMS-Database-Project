<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SavingsAccount;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class SavingsAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = SavingsAccount::class;

    public function definition(): array
    {
        return [
            'account_number' => 'SA-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'account_type_id' => $this->faker->numberBetween(1, 3), // assumes you have 3 account types
            'branch_id' => $this->faker->numberBetween(1, 5), // assumes 5 branches
            'balance' => $this->faker->randomFloat(2, 1000, 50000),
            'status' => $this->faker->randomElement(['ACTIVE', 'INACTIVE']),
            'opened_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'closed_date' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'last_transaction_date' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
