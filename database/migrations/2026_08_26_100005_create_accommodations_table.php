<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the accommodations table for hotel/stay options
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // option name like "No Accommodation" or "3 Star Hotel"
            $table->tinyInteger('star_rating')->nullable(); // optional star rating from 1 to 5
            $table->decimal('price_per_night', 8, 2); // cost of one night for this option
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
