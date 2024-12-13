<?php

namespace Database\Seeders;

use App\Models\Agenda_Tecnico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tecnico;

class TecnicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                // Por cada usuario, crear varios clientes internos
                Tecnico::factory()
                    ->count(1) // por ejemplo 5 clientes internos por usuario
                    ->create([
                        'userId' => $user->id,
                    ])
                    ->each(function ($technician){
                        Agenda_Tecnico::factory()
                            ->create([
                                'technicianId'=>$technician->id
                            ]);
                    });
            });
    }
}
