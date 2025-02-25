<?php

namespace Database\Factories;

use App\Models\Solicitud;
use App\Models\Historial_Servicios;
use App\Models\Cliente_Interno;
use App\Models\Tecnico;
use App\Models\Tipo_Actividad;
use App\Models\Tipo_Estado;
use Carbon\Carbon;

class RequestFactorySeeder
{
    /**
     * Genera solicitudes de prueba.
     */
    public static function create(int $count = 10)
    {
        // Obtener registros existentes de otras tablas
        $clients = Cliente_Interno::all();
        $technicians = Tecnico::all();
        $activities = Tipo_Actividad::all();
        $states = Tipo_Estado::all();

        // Verificar que existan datos relacionados
        if ($clients->isEmpty() || $technicians->isEmpty() || $activities->isEmpty() || $states->isEmpty()) {
            throw new \Exception('No hay datos suficientes para poblar la tabla requests.');
        }

      // Crear solicitudes y registrar historial
      foreach (range(1, $count) as $index) {
        $client = $clients->random();
        $technician = $technicians->random();
        $activity = $activities->random();
        $state = $states->random();

        // Crear la solicitud
        $request = Solicitud::factory()->create([
            'clientId' => 41,
            'technicianId' => $technician->id,
            'activityId' => $activity->id,
            'stateId' =>  rand(1,3),
            'status' => 1,  // Estado activo
            'registrationDateTime' => Carbon::create(2025, rand(2, 3), rand(1, 28), rand(0, 23), rand(0, 59), rand(0, 59))
        ]);

        // Crear el historial de la solicitud
        Historial_Servicios::create([
            'clientId' => $request->clientId,
            'technicianId' => $request->technicianId,
            'jobId' => $request->id,
            'descriptionJob' => 1,  // 1 = solicitud creada
            'stateId' => $request->stateId,
            'outsetDate' => $request->registrationDateTime,
            'description' => $request->requestDescription,
        ]);
    }
    }
}
