<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Maintenance;
use App\Models\Vehicule;

class MaintenanceSeeder extends Seeder
{
    public function run()
    {
        Maintenance::factory()->count(30)->create()->each(function ($maintenance) {
            // Récupérer quelques véhicules aléatoires (1 à 3 par maintenance)
            $vehiculeIds = Vehicule::inRandomOrder()->limit(rand(1, 3))->pluck('id');

            // Attacher les véhicules à la maintenance (table pivot)
            $maintenance->vehicles()->attach($vehiculeIds);
        });
    }
}
