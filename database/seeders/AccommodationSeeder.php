<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;

class AccommodationSeeder extends Seeder
{
    // reads accommodations.json and saves each accommodation option into the database
    public function run(): void
    {
        // load the JSON file from database/data
        $items = json_decode(file_get_contents(database_path('data/accommodations.json')), true);

        // loop through each item and save it
        foreach ($items as $item) {
            Accommodation::create([
                'name' => $item['name'],
                'star_rating' => $item['star_rating'] ?? null,
                'price_per_night' => $item['price_per_night'],
            ]);
        }
    }
}
