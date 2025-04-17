<?php

namespace Database\Seeders;

use App\Models\Agenda_Tecnico;
use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Technician_subcripcion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Tecnico_Habilidad;
use Carbon\Carbon;
use Nette\Utils\Random;

class TecnicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(3)
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
                        Tecnico_Habilidad::create([
                            'experience' => rand(1,4).'año',
                            'technicianId'=>$technician->id,
                            'skillId'=>rand(1,20)
                        ]);
                        Technician_subcripcion::create([
                            'technicianId'=>$technician->id,
                            'subcriptionsId'=>rand(1,4),
                            'starDateSubcription'=> now()->addMonth(),
                            'endDateSubcription' => now()->addDays(7),
                            'status' => 1
                        ]);
                        $device = Devices::create([
                            'expo_token' => 'xx345678xxxxxxxx09876' ,
                            'type_device' => 'Android' ,
                            'name_device' => 'Samsung mini s3'
                        ]);

                        DevicesUser::create([
                            'device_id' => $device->id,
                            'users_id'   => $technician->user->id
                        ]);
                    });
            });
    }
}
