<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the transports table for travel options like Bus, Train, Car, Plane
    public function up(): void
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // transport name like "Bus" or "Train"
            $table->decimal('price_per_km', 8, 2); // price per kilometer for this transport type
            $table->string('icon')->nullable(); // optional icon name or path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
