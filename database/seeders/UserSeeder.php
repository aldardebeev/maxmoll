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
            'email' => 'sasha@mail',
            'password' => '12345678',
        ]);
        User::create([
            'name' => 'Иван',
            'email' => 'ivan@mail',
            'password' => '12345678',
        ]);
        User::create([
            'name' => 'Данил',
            'email' => 'danil@mail',
            'password' => '12345678',
        ]);
        User::create([
            'name' => 'Миша',
            'email' => 'misha@mail',
            'password' => '12345678',
        ]);
        User::create([
            'name' => 'Алдар',
            'email' => 'aldar@mail',
            'password' => '12345678',
        ]);


    }
}
