<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the trip_plans table for saved trip cost estimates
    public function up(): void
    {
        Schema::create('trip_plans', function (Blueprint $table) {
            $table->id();
            // starting location chosen for the trip
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            // destination place chosen for the trip
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            // transport mode chosen for the trip
            $table->foreignId('transport_id')->constrained('transports')->onDelete('cascade');
            // accommodation option chosen for the trip
            $table->foreignId('accommodation_id')->constrained('accommodations')->onDelete('cascade');
            $table->unsignedInteger('people'); // number of people travelling
            $table->unsignedInteger('days'); // number of days for the trip
            $table->decimal('transport_cost', 10, 2); // calculated cost for transport
            $table->decimal('accommodation_cost', 10, 2); // calculated cost for stay
            $table->decimal('total_cost', 10, 2); // total estimated trip cost
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_plans');
    }
};
