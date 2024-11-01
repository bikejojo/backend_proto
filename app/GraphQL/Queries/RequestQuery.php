<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Solicitud;

final readonly class RequestQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function requestsTechnicalId($root , array $args){
        $technicalId = $args['id'];
        $technical = Tecnico::find($technicalId);
        //dd($technical);
        if(!isset($technical)){
            return [
                'message' => 'No existe tecnico'
            ];
        }

        $technicaId = $technical->id;
        $request = Solicitud::where('technicianId',$technicaId)->get();
        if(!isset($request)){
            return [
                'message' => 'No existe solicitudes del tecnico.'
            ];
        }
        return [
            'message' => 'Solicitudes del tecnico.',
            'request' => $request,
            'technical' =>  $technical
        ];
    }
}
