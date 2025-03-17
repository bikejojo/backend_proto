<?php

namespace Database\Seeders;

use App\Models\Agenda_Tecnico;
use App\Models\Technician_subcripcion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tecnico;
use Carbon\Carbon;

class TecnicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(8)
            ->create()
            ->each(function ($user) {
                // Por cada usuario, crear varios clientes internos
                Tecnico::factory()
                    ->count(1) // por ejemplo 5 clientes internos por usuario
                    ->create([
                        'userId' => $user->id,
                    ])
                    ->each(function ($technician){
                        Agenda_Tecnico::create([
                                'technicianId'=>$technician->id,
                                'createDate'=>Carbon::now()
                        ]);
                        Technician_subcripcion::create([
                            'technicianId'=>$technician->id,
                            'subcriptionsId'=>1,
                            'starDateSubcription'=> now()->addMonth(),
                            'endDateSubcription' => now()->addMonths(1),
                            'status' => 1
                        ]);
                    });
            });
    }
}
