<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Helpers\StatusHelper;
use Illuminate\Support\Facades\Validator;
use App\Models\Tecnico;
use App\Models\Servicio;
use App\Models\Calificacion;
use App\Services\StatusAssigner;

class RatingMutations
{
    public function rateService($root,array $args){
        $ratingData = $args['requestRating'];
        $serviceId = $ratingData['id_service'];
        $service =Servicio::find($serviceId);
        if($service->stateId !== 5){
            return [
                'message'=>'El servicio no esta terminado.'
            ];
        }
        $ratingData = $args['requestRating'];
            $rating = Calificacion::create([
                'technicialId' => $ratingData['id_technician'],
                'serviceId'=> $ratingData['id_service'],
                'clientId' => $ratingData['id_client'],
                'rating' => $ratingData['rating'] ,
                'feedback' => $ratingData['comments']
            ]);
        return [
            'message' => 'Su calificacion fue registrada' ,
            'service' => $service,
            'rating' => $rating
        ];

    }

}
