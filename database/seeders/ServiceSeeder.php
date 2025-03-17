<?php

namespace Database\Seeders;

use App\Models\Servicio;
use App\Models\Solicitud;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $solicitudes = Solicitud::where('stateId', 2)->get();

        // Si no hay solicitudes, no hacer nada
        if ($solicitudes->isEmpty()) {
            return;
        }

        foreach ($solicitudes as $solicitud) {
            Servicio::factory()->create([
                'requestsId' => $solicitud->id,
                'titleService' => $solicitud->titleRequests,
                'serviceDescription' => $solicitud->requestDescription,
                'status' => 1 ,
                'technicalId' => $solicitud->technicianId,
                'clientId' => $solicitud->clientId,
                'service_origin' => 1 ,
                'updatedDateTime' => now()
            ]);
        }
    }
}
