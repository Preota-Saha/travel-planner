<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\PlaceExperience;
use Illuminate\Database\Seeder;

class PlaceExperienceSeeder extends Seeder
{
    // reads place_experiences.json and saves each experience, looking up its place by slug
    public function run(): void
    {
        // load the JSON file from database/data
        $items = json_decode(file_get_contents(database_path('data/place_experiences.json')), true);

        // loop through each experience and save it with its place id
        foreach ($items as $item) {
            // find the matching place by its slug
            $place = Place::where('slug', $item['place_slug'])->first();

            PlaceExperience::create([
                'place_id' => $place->id,
                'type' => $item['type'],
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'image' => $item['image'] ?? null,
            ]);
        }
    }
}
