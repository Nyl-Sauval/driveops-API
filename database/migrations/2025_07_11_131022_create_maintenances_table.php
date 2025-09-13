<?php

use App\Models\Maintenance;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [Maintenance::TYPES]);
            $table->text('description')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->integer('scheduled_mileage')->nullable();
            $table->boolean('done')->default(false);
            $table->date('done_date')->nullable();
            $table->integer('done_mileage')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
        });

        // Table pivot pour la relation many-to-many avec Vehicule
        Schema::create('maintenance_vehicule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_id')->constrained('maintenances')->onDelete('cascade');
            $table->foreignId('vehicule_id')->constrained('vehicules')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_vehicule');
        Schema::dropIfExists('maintenances');
    }
};
