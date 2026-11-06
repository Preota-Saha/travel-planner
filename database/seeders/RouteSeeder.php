<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Place;
use App\Models\Route;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    // reads routes.json and saves each route, looking up location by name and place by slug
    public function run(): void
    {
        // load the JSON file from database/data
        $items = json_decode(file_get_contents(database_path('data/routes.json')), true);

        // loop through each route and save it with its location id and place id
        foreach ($items as $item) {
            // find the matching location and place
            $location = Location::where('name', $item['location_name'])->first();
            $place = Place::where('slug', $item['place_slug'])->first();

            Route::create([
                'location_id' => $location->id,
                'place_id' => $place->id,
                'distance_km' => $item['distance_km'],
            ]);
        }
    }
}
