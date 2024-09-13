<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::all()->each(function (\App\Models\Product $product) {
            \App\Models\ProductImage::factory(4)->create([
                'product_id' => $product->id,
            ]);
        });
    }
}
