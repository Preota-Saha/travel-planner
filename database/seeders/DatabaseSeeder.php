<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // seed travel planner data first (order matters because of foreign keys)
        $this->call([
            CategorySeeder::class,
            PlaceSeeder::class,
            PlaceExperienceSeeder::class,
            LocationSeeder::class,
            TransportSeeder::class,
            AccommodationSeeder::class,
            RouteSeeder::class,
        ]);
    }
}
