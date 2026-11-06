<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the place_experiences table for attractions, food and shopping
    public function up(): void
    {
        Schema::create('place_experiences', function (Blueprint $table) {
            $table->id();
            // place this experience belongs to
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            // kind of experience: attraction, food, or shopping
            $table->enum('type', ['attraction', 'food', 'shopping']);
            $table->string('title'); // short title of the experience
            $table->text('description')->nullable(); // optional longer description
            $table->string('image')->nullable(); // optional image path or url
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_experiences');
    }
};
