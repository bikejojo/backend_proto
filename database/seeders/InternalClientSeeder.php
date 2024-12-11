<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cliente_Interno;
use App\Models\User;
use Illuminate\Database\Seeder;

class InternalClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear varios usuarios
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                // Por cada usuario, crear varios clientes internos
                Cliente_Interno::factory()
                    ->count(1) // por ejemplo 5 clientes internos por usuario
                    ->create([
                        'userId' => $user->id,
                    ]);
            });
    }
}

