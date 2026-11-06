<?php

namespace Database\Seeders;

use App\Models\Transport;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    // reads transports.json and saves each transport option into the database
    public function run(): void
    {
        // load the JSON file from database/data
        $items = json_decode(file_get_contents(database_path('data/transports.json')), true);

        // loop through each item and save it
        foreach ($items as $item) {
            Transport::create([
                'name' => $item['name'],
                'price_per_km' => $item['price_per_km'],
                'icon' => $item['icon'] ?? null,
            ]);
        }
    }
}
