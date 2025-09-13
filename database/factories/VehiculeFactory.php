<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicule>
 */
class VehiculeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'brand' => fake()->word(),
            'model' => fake()->word(),
            'year' => fake()->year(),
            'mileage' => fake()->numberBetween(1000, 200000),
            'license_plate' => fake()->bothify('??-####'),
            //user_id need to exist in the users table
            'user_id' => fake()->numberBetween(1, 10), // Adjust the range based on your users table
        ];
    }
}
