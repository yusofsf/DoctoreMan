<?php

namespace Database\Factories;

use App\Enums\AdminStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'display_name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement(array_column(AdminStatus::cases(), 'value')),
            'user_id' => User::factory(),
        ];
    }
}
