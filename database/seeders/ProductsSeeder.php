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
            'name' => 'Телефон',
            'price' => 1000.99,
            'stock' => 100
        ]);
        Product::create([
            'name' => 'Телевизор',
            'price' => 11000.99,
            'stock' => 100
        ]);
        Product::create([
            'name' => 'Компьютер',
            'price' => 111000.99,
            'stock' => 100
        ]);
    }
}