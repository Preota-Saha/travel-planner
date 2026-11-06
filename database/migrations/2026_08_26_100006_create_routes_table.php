<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the routes table for pre-seeded distances between locations and places
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            // starting location for the trip
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            // destination place for the trip
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->decimal('distance_km', 8, 2); // distance in kilometers between them
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
