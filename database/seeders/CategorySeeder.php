<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    // reads categories.json and saves each category into the database
    public function run(): void
    {
        // load the JSON file from database/data
        $items = json_decode(file_get_contents(database_path('data/categories.json')), true);

        // loop through each item and save it
        foreach ($items as $item) {
            Category::create([
                'name' => $item['name'],
                'slug' => $item['slug'],
                'image' => $item['image'] ?? null,
            ]);
        }
    }
}
