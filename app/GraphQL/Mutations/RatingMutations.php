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
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function rateService($root,array $args){
        $validator = Validator::make($args['requestRating'],[
            'id_technician' => 'requires|exists:technicians.id',
            'id_client' =>'requires|exists:internal_clients.id',
            'id_service' => 'required!exists:services.id',
            'rating' => 'required!integer|min:1|max:5',
            'comments' => 'nullable!string'
        ]);
        if ($validator->fails()) {
            return [
                'message' => 'Validation failed',
                'service' => $validator->errors(),
            ];
        }
        $ratingData = $args['requestRating'];
        $serviceId = $ratingData['id_service'];
        $service =Servicio::find($serviceId);
        if($service->stateId !== 5){
            return [
                'message'=>'El servicio no esta terminado.'
            ];
        }
        $ratingData = $args['requestRating'];
            $rating = Calificacion::updateOrCreate([
                'id_technician' => $ratingData['id_technician'],
                'id_service'=> $ratingData['id_service'],
                'rating' => $ratingData['rating'] ,
                'comming' => $ratingData['comming']
            ]);
        return [
            'message' => 'Su calificacion fue registrada' ,
            'service' => $service,
            'rating' => $rating
        ];

    }
}
