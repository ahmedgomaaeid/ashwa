<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\Category::all();
        foreach ($categories as $category) {
            foreach($category->sections as $section) {
                \App\Models\Product::factory(5)->create([
                    'category_id' => $category->id,
                    'section_id' => $section->id,
                ]);
            }
        }
    }
}
