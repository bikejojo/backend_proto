<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Cliente_Externo;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Tecnico;
use Illuminate\Database\Seeder;

class ExternalClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $tecnico = 19;
                // Crea 1000 clientes
        Cliente_Externo::factory(100)->create()->each(function ($client) use ($tecnico) {
            // Asocia cada cliente a un técnico de forma aleatoria
            Asociacion_Cliente_Tecnico::create([
                'clientId' => $client->id,
                'technicalId' => $tecnico, // Asocia con un técnico aleatorio
                'dateTimeCreated' => now(),
            ]);
        });
    }
}
