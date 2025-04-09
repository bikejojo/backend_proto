<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use Illuminate\Support\Facades\DB;
use App\Models\Servicio;
use App\Models\Calificacion;
use App\Models\Cliente_Interno;
use App\Services\StatusAssigner;
use Carbon\Carbon;

class RatingMutations{
    public static $entity_type = "service";

    public function rateService($root, array $args){
        try {
            $ratingData = $args['requestRating'];
            //dd($ratingData);
            $ratingsArray = is_array($ratingData[0] ?? null)
                ? $ratingData
                : [$ratingData];

            //$validRatings = [];  // los que se van a registrar
            $responses = [];
            DB::beginTransaction();
            foreach ($ratingsArray as $rating) {
                //dd($rating['id_service']);
                $client = Cliente_Interno::find($rating['id_client']);
                $technician = Tecnico::find($rating['id_technician']);
                $service = Servicio::join('state_reference', 'services.id', '=', 'state_reference.serviceId')
                    ->where('services.stateId', 4)
                    ->where('services.id', $rating['id_service'])
                    ->select(
                        'services.id',
                        'services.titleService',
                        'services.serviceDescription',
                    )
                    ->first();
                //dd($service);
                if (Calificacion::where('serviceId', $rating['id_service'])->where('technicialId', $rating['id_technician'])->where('clientId', $rating['id_client'])->exists()) {
                    $responses[] = [
                        'message' => 'Este servicio ya tiene una calificación.',
                        'service' => $service,
                        'rating' => Calificacion::where('serviceId', $rating['id_service'])->where('technicialId', $rating['id_technician'])->where('clientId', $rating['id_client'])->first(),
                        'client' => $client
                    ];
                    continue;
                }

                $service->stateId = 5;
                $service->finishDateTime_client = now();
                $service->save();

                $calificacion = Calificacion::create([
                    'serviceId' => $rating['id_service'],
                    'technicialId' => $rating['id_technician'],
                    'clientId' => $rating['id_client'],
                    'rating' => $rating['rating'],
                    'comments' => $rating['comments'] ?? null,
                ]);

                $responses[] = [
                    'message' => 'Calificación registrada correctamente.',
                    'rating' => $calificacion,
                    'client' => $client,
                    'service' => $service
                ];
            }
            DB::commit();
            return [
                'message' => 'Proceso de calificaciones completado.',
                'responses' => $responses
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
