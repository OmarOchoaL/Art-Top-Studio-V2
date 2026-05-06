<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
        [
            'category_id' => 1,
            'name' => 'Taza blanca personalizada',
            'price' => 120,
            'description' => 'Taza sublimada con diseño personalizado'
        ],
        [
            'category_id' => 2,
            'name' => 'Termo de acero',
            'price' => 250,
            'description' => 'Termo personalizable con grabado láser'
        ],
    ]);
    }
}
