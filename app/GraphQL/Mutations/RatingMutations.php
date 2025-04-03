<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\Servicio;
use App\Models\Calificacion;
use App\Models\Cliente_Interno;
use App\Services\StatusAssigner;

class RatingMutations{
    public function rateService($root, array $args){
        try {
            $ratingData = $args['requestRating'];

            $ratingsArray = is_array($ratingData[0] ?? null)
                ? $ratingData
                : [$ratingData];

            $validRatings = [];  // los que se van a registrar
            $responses = [];

            foreach ($ratingsArray as $i => $resp) {
                $client = Cliente_Interno::find($resp['id_client']);
                $technician = Tecnico::find($resp['id_technician']);
                $service = Servicio::join('state_reference','services.id','=','state_reference.serviceId')
                        ->where('services.stateId',4)
                        ->where('services.id', $resp['id_service'])
                        ->first();
                $exists = Calificacion::where('serviceId', $resp['id_service'])
                        ->where('technicialId', $resp['id_technician']) // asegúrate que este nombre esté bien
                        ->where('clientId', $resp['id_client'])
                        ->exists();
                if ($exists) {
                    $responses[] = [
                        'service' => $service,
                        'technician' => $technician,
                        'client' => $client,
                        'message' => 'Este servicio ya tiene una calificación.'
                    ];
                    continue;
                }

                // Agrega a la lista de calificaciones válidas
                $validRatings[] = $resp;
            }

            // Ahora procesamos las válidas
            foreach ($validRatings as $rating) {
                $calificacion = Calificacion::create([
                    'serviceId' => $rating['id_service'],
                    'technicialId' => $rating['id_technician'],
                    'clientId' => $rating['id_client'],
                    'rating' => $rating['rating'],
                    'comments' => $rating['comments'],
                ]);

                $responses[] = [
                    'technician' => $technician,
                    'client' => $client,
                    'service' => $service,
                    'rating' => $calificacion,
                    'message' => 'Calificación registrada correctamente.'
                ];
            }

            return [
                'message' => 'Proceso de calificaciones completado.',
                'responses' => $responses
            ];

        } catch (\Exception $e) {
            return [
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
