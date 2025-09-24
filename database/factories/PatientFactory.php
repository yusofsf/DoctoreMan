<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gender' => $this->faker->randomElement(array_column(Gender::cases(), 'value')),
            'birth_date' => $this->faker->date(),
            'phone_number' => $this->faker->phoneNumber(),
        ];
    }
}
