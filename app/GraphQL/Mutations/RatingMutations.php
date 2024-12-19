<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Helpers\StatusHelper;
use Illuminate\Support\Facades\Validator;
use App\Models\Tecnico;
use App\Models\Servicio;
use App\Models\Calificacion;
use App\Services\StatusAssigner;
use App\Services\StateCatalog;
use Nuwave\Lighthouse\Federation\Resolvers\Service;

class RatingMutations{
    public function rateService($root, array $args){
        $ratingData = $args['requestRating'];
        $serviceId = $ratingData['id_service'];

        $service = Servicio::join('state_reference','services.id','=','state_reference.referenceId')
                    ->where('state_reference.stateId',4)
                    ->where('state_reference.type','service')
                    ->where('descriptionState',StatusAssigner::SERVICE_COMPLETED_T)
                    ->first();
        //dd($service->stateId);
        if (!$service) {
            return [
                'message' => 'Servicio no encontrado.',
            ];
        }

        // Verificar si el estado actual del servicio es "Terminado" (stateId = 5)
        //$stateReference = $service->stateReference;
        if (!$service->stateId || $service->stateId !== 4) {
            return [
                'message' => 'El servicio no está terminado.',
            ];
        }

        // Crear la calificación
        $rating = Calificacion::create([
            'technicialId' => $ratingData['id_technician'],
            'serviceId' => $ratingData['id_service'],
            'clientId' => $ratingData['id_client'],
            'rating' => $ratingData['rating'],
            'feedback' => $ratingData['comments']
        ]);

        // Verificar si el técnico existe
        $technician = Tecnico::find($rating->technicialId);
        if (!$technician) {
            return [
                'message' => 'Técnico no encontrado.',
                'average_rating' => null,
                'ratings_count' => 0,
            ];
        }

        // Calcular promedio de calificaciones del técnico
        $ratingsCount = Calificacion::where('technicialId', $technician->id)->count();
        $ratingsSum = Calificacion::where('technicialId', $technician->id)->sum('rating');
        $averageRating = $ratingsCount > 0 ? $ratingsSum / $ratingsCount : 0;
        $roundedRating = round($averageRating * 2) / 2;
        $technician->average_rating = $roundedRating;
        $technician->save();

        return [
            'message' => 'Su calificación fue registrada',
            'service' => $service,
            'rating' => $rating
        ];
    }
}
