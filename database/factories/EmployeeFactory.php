<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('0#########'),
            'position' => $this->faker->randomElement(['Manager', 'Teller', 'Loan Officer', 'Customer Service', 'Accountant']),
            'nic_num' => $this->faker->numerify('##########V'),
            'branch_id' => Branch::factory(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
