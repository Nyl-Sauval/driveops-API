<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Maintenance;

class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;

    public function definition()
    {
        $type = $this->faker->randomElement(Maintenance::TYPES);

        return [
            'type' => $type,
            'description' => $this->faker->sentence(),
            'scheduled_date' => $type === Maintenance::TYPE_TIME ? $this->faker->dateTimeBetween('now', '+1 year') : null,
            'scheduled_mileage' => $type === Maintenance::TYPE_MILEAGE ? $this->faker->numberBetween(1000, 20000) : null,
            'done' => $this->faker->boolean(70), // 70% chance done
            'done_date' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'done_mileage' => $this->faker->boolean(70) ? $this->faker->numberBetween(1000, 20000) : null,
            'cost' => $this->faker->optional()->randomFloat(2, 50, 1000),
        ];
    }
}
