<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CustomerStatusType;
use App\Models\Branch;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */




    public function definition(): array
    {
        $statusIds = CustomerStatusType::pluck('id')->toArray();
        $branchIds = Branch::pluck('id')->toArray();
        $employeeIds = Employee::pluck('id')->toArray();

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->dateTimeBetween('-70 years', '-10 years'), // random DOB
            'gender' => $this->faker->randomElement(['M', 'F', 'Other']),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'id_type' => $this->faker->randomElement(['NIC', 'Passport', 'Driving License']),
            'id_number' => $this->faker->unique()->numerify('#########'), // random 9-digit ID
            'status_id' => $this->faker->randomElement($statusIds),
            'employee_id' => $this->faker->randomElement($employeeIds),
            'branch_id' => $this->faker->randomElement($branchIds),
        ];
    }
}
