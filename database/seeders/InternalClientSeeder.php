<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cliente_Interno;
use App\Models\User;
use App\Models\Devices;
use App\Models\DevicesUser;

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
            ->count(3)
            ->create()
            ->each(function ($user) {
                // Por cada usuario, crear varios clientes internos
                Cliente_Interno::factory()
                    ->count(1) // por ejemplo 5 clientes internos por usuario
                    ->create([
                        'userId' => $user->id,
                    ]);
                    $device = Devices::create([
                        'expo_token' => 'xx345678xxxxxxxx09876' ,
                        'type_device' => 'Android' ,
                        'name_device' => 'Samsung mini s3'
                    ]);

                    DevicesUser::create([
                        'device_id' => $device->id,
                        'users_id'   => $user->id
                    ]);
            });
    }
}

