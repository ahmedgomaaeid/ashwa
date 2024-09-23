<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\Category::all();
        foreach ($categories as $category) {
            \App\Models\Section::factory(5)->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
