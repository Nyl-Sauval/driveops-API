<?php

namespace Database\Seeders;

use App\Models\Vehicule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehiculeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use a factory to create vehicules or manually insert them
        // Example of using a factory (assuming you have a VehiculeFactory):
        Vehicule::factory(10)->create();

        // Or manually inserting some example data:
        \App\Models\Vehicule::create([
            'name' => 'Test Car',
            'brand' => 'Test Brand',
            'model' => 'Model X',
            'year' => 2020,
            'mileage' => 15000,
            'license_plate' => 'TEST123',
            'user_id' => 1, // Assuming user with ID 1 exists
        ]);
    }
}
