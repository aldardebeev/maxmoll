<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductMovement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductMovementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductMovement::create([
            'product_id' => 4,
            'warehouse_from_id' => 1,
            'warehouse_to_id' => 2,
            'quantity' => 10,
            'movement_type' => 'transfer'
        ]);

    }
}
