<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the places table for destinations inside Bangladesh
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            // category this place belongs to (Hills, Sea, etc.)
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name'); // place name like "Cox's Bazar"
            $table->string('slug')->unique(); // url-friendly version of the name
            $table->text('description'); // short description of the place
            $table->string('cover_image')->nullable(); // optional cover image path or url
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
