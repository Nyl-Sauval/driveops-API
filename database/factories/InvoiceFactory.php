<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = \App\Models\Invoice::class;

    public function definition()
    {
        return [
            'date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'amount' => $this->faker->randomFloat(2, 20, 2000),
            'description' => $this->faker->sentence(),
            'file_path' => null, // ou faker file path selon besoin
        ];
    }
}
