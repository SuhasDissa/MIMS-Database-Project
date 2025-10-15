<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\SavingsAccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavingsAccount>
 */
class SavingsAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $openedDate = $this->faker->dateTimeBetween('-5 years', 'now');

        return [
            'account_number' => $this->faker->unique()->numerify('SA##########'),
            'account_type_id' => $this->faker->numberBetween(1, 3), // Student, Regular, Senior, Premium Savings
            'branch_id' => $this->faker->numberBetween(1, 10), // Use existing branches 1-10
            'balance' => 0,
            'status' => $this->faker->randomElement(['ACTIVE', 'INACTIVE']),
            'opened_date' => $openedDate,
            'closed_date' => null,
            'last_transaction_date' => $this->faker->dateTimeBetween($openedDate, 'now'),
        ];
    }

    /**
     * Indicate that the account is inactive/closed.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'INACTIVE',
            'closed_date' => $this->faker->dateTimeBetween($attributes['opened_date'], 'now'),
            'balance' => 0,
        ]);
    }

    /**
     * Indicate that the account is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ACTIVE',
            'closed_date' => null,
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($savingsAccount) {
            // Attach 1-3 random customers to the savings account
            $customerCount = $this->faker->numberBetween(1, 2);
            $customers = Customer::inRandomOrder()->limit($customerCount)->get();

            if ($customers->isEmpty()) {
                // If no customers exist, create them
                $customers = Customer::factory($customerCount)->create();
            }

            $savingsAccount->customers()->attach($customers->pluck('id'));
        });
    }
}
