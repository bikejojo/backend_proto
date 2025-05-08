<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Events\ServicioCompletado;
use App\Events\ServicioTerminado;
use App\Models\Tecnico;
use Illuminate\Support\Facades\DB;
use App\Models\Servicio;
use App\Models\Calificacion;
use App\Models\Cliente_Interno;
use App\Models\Solicitud;
use App\Services\StatusAssigner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RatingMutations{
    public static $entity_type = "service";

    public function rateService($root, array $args){
        try {
            $ratingData = $args['requestRating'];

            $ratingsArray = is_array($ratingData[0] ?? null)
                ? $ratingData
                : [$ratingData];

            $responses = [];
            DB::beginTransaction();
            foreach ($ratingsArray as $rating) {
                $client = Cliente_Interno::find($rating['id_client']);
                $technician = Tecnico::find($rating['id_technician']);

                if (Calificacion::where('serviceId', $rating['id_service'])->where('technicialId', $rating['id_technician'])->where('clientId', $rating['id_client'])->exists()) {
                   $existingRating = Calificacion::where('serviceId', $rating['id_service'])
                    ->where('technicialId', $rating['id_technician'])
                    ->where('clientId', $rating['id_client'])
                    ->first();

                    $service_= Servicio::find($rating['id_service']);

                    $responses[] = [
                        'message' => 'Este servicio ya tiene una calificación.',
                        'service' => $service_,
                        'rating' => $existingRating,
                        'client' => $client
                    ];

                    continue;
                }

                $service = Servicio::where('services.stateId', 5)
                    ->where('services.id', $rating['id_service'])
                    ->select(
                        'services.id',
                        'services.titleService',
                        'services.serviceDescription',
                        'services.stateId',
                        'services.requestsId',
                        'services.finishDateTime_technician',
                        'services.updatedDateTime',
                    )
                    ->first();
                    //dd($service);
                if(!$service){
                    DB::rollBack();
                    Log::info('Surgio un problema con servicio');
                    return [
                        'message' => 'Error: No se encontró la solicitud asociada al servicio.'
                    ];
                }
                $service->stateId = 4;
                $service->finishDateTime_client = now();
                $service->save();
                Log::info('Servicios' , ['service' => $service->toArray()]);

                event(new ServicioTerminado($service));
                $request= Solicitud::where('id',$service->requestsId )->first();
                if(!$request){
                    DB::rollBack();
                    Log::info('Surgio un problema con solicitud');
                    return [
                        'message' => 'Error: No se encontró la solicitud asociada al servicio.'
                    ];
                }
                $request->stateId = 4;
                $request->save();
                //Log::info('solicitudes' , ['request' => $request->toArray()]);
                $ratingsSum = 0;
                $ratingsCount = 0;
                    $ratingsSum = $rating['rating'] + $technician->average_rating;
                    $ratingsCount = 2;
                    $average = $ratingsSum / $ratingsCount;
                    $rounded = round($average * 4) / 4;
                    $technician->average_rating = number_format($rounded, 2);
                    $technician->save();

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
                    'service' => $service,
                    'technician' => $technician
                ];

            }
            DB::commit();
            return [
                'message' => 'Proceso de calificaciones completado.',
                'responses' => $responses
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Surgio un problema al momento de calificar'. $e->getMessage());
            return [
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
