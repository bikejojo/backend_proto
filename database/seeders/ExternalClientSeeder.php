<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente_Externo;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Technician;
use App\Models\Tecnico;

class ExternalClientSeeder extends Seeder
{
    public function run(): void
    {
        $technicians = Tecnico::all();

        if ($technicians->isEmpty()) {
            $this->command->warn('No hay técnicos registrados. Ejecute el seeder de técnicos primero.');
            return;
        }

        // Crear 20 clientes externos y asociarlos a técnicos
        Cliente_Externo::factory(2)->create()->each(function ($cliente) use ($technicians) {
            $technician = $technicians->random();

            // Crear la asociación usando factory
            Asociacion_Cliente_Tecnico::factory()->create([
                'technicalId' => $technician->id,
                'clientId' => $cliente->id,
            ]);
        });

        $this->command->info('Clientes externos y asociaciones creados exitosamente.');
    }
}
