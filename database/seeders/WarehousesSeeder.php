<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class WarehousesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'name' => 'Склад 1',
        ]);
        Warehouse::create([
            'name' => 'Склад 2',
        ]);
        Warehouse::create([
            'name' => 'Склад 3',
        ]);
        Warehouse::create([
            'name' => 'Склад 4',
        ]);
        Warehouse::create([
            'name' => 'Склад 5',
        ]);


    }
}
