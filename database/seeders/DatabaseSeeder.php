<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SectionSeeder::class,
            ProductSeeder::class,
            ProductImageSeeder::class,
            OfferSeeder::class,
            // Add more seeders here
        ]);
    }
}
