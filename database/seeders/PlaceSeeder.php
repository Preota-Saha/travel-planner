<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    // reads places.json and saves each place, looking up its category by slug
    public function run(): void
    {
        // load the JSON file from database/data
        $items = json_decode(file_get_contents(database_path('data/places.json')), true);

        // loop through each place and save it with its category id
        foreach ($items as $item) {
            // find the matching category by its slug
            $category = Category::where('slug', $item['category_slug'])->first();

            Place::create([
                'category_id' => $category->id,
                'name' => $item['name'],
                'slug' => $item['slug'],
                'description' => $item['description'],
                'cover_image' => $item['cover_image'] ?? null,
            ]);
        }
    }
}
