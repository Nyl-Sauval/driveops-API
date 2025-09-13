<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        Invoice::factory()->count(20)->create()->each(function ($invoice) {
            // Optionnel : lier aléatoirement véhicules et maintenances
            $vehiculeIds = \App\Models\Vehicule::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            $maintenanceIds = \App\Models\Maintenance::inRandomOrder()->limit(rand(1, 3))->pluck('id');

            $invoice->vehicles()->attach($vehiculeIds);
            $invoice->maintenances()->attach($maintenanceIds);
        });
    }
}
