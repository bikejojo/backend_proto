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
                $client = Cliente_Interno::find($rating['id_client']);
                $technician = Tecnico::find($rating['id_technician']);

                $service = Servicio::join('state_reference', 'services.id', '=', 'state_reference.serviceId')
                    ->where('services.stateId', 5)
                    ->where('services.id', $rating['id_service'])
                    ->select(
                        'services.id',
                        'services.titleService',
                        'services.serviceDescription',
                    )
                    ->first();
                //dd($service);
                if (Calificacion::where('serviceId', $rating['id_service'])->where('technicialId', $rating['id_technician'])->where('clientId', $rating['id_client'])->exists()) {
                   $existingRating = Calificacion::where('serviceId', $rating['id_service'])
                    ->where('technicialId', $rating['id_technician'])
                    ->where('clientId', $rating['id_client'])
                    ->first();

                    $service = Servicio::find($rating['id_service']);

                    $responses[] = [
                        'message' => 'Este servicio ya tiene una calificación.',
                        'service' => $service,
                        'rating' => $existingRating,
                        'client' => $client
                    ];

                    continue;
                }

                $service->stateId = 4;
                $service->finishDateTime_client = now();
                $service->save();

                //dd($ratingsSum);
                // Agregar la calificación virtual de 5 si aún no tiene reales
                $ratingsSum = 0;
                $ratingsCount = 0;
                if (!Calificacion::where('technicialId', $technician->id)->exists()) {
                   // sumando la nueva calificación
                    $ratingsSum = $rating['rating'] + $technician->average_rating;
                    $ratingsCount = 2;
                    $average = $ratingsSum / $ratingsCount;
                    $rounded = round($average * 4) / 4;
                    $technician->average_rating = number_format($rounded, 2);
                    $technician->save();
                }else{
                    $ratingsSum = $rating['rating'] + $technician->average_rating;
                    $ratingsCount = 2;
                    $average = $ratingsSum / $ratingsCount;
                    $rounded = round($average * 4) / 4;
                    $technician->average_rating = number_format($rounded, 2);
                    $technician->save();
                }

                // Calcular nuevo promedio


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
