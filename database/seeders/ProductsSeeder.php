<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Ноутбук',
            'price' => 1000,
            'stock' => 100
        ]);
        Product::create([
            'name' => 'Наушники',
            'price' => 11000,
            'stock' => 100
        ]);
        Product::create([
            'name' => 'Клавиатура',
            'price' => 111000,
            'stock' => 100
        ]);
    }
}
