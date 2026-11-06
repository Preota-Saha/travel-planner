<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    // reads locations.json and saves each starting location into the database
    public function run(): void
    {
        // load the JSON file from database/data
        $items = json_decode(file_get_contents(database_path('data/locations.json')), true);

        // loop through each item and save it
        foreach ($items as $item) {
            Location::create([
                'name' => $item['name'],
            ]);
        }
    }
}
