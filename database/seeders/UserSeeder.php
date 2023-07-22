<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Александр',
        ]);
        User::create([
            'name' => 'Иван',
        ]);
        User::create([
            'name' => 'Данил',
        ]);
        User::create([
            'name' => 'Миша',
        ]);
        User::create([
            'name' => 'Алдар',
        ]);


    }
}
