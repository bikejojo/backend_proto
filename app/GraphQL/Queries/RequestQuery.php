<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Cliente_Interno;
use App\Models\Tecnico;
use App\Models\Solicitud;

use function PHPUnit\Framework\isEmpty;

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
        $request = Solicitud::where('technicianId',$technicaId)
        ->orderBy('registrationDateTime','DESC')
        ->get();
        if(isset($request)){
            return [
                'message' => 'No existe solicitudes del tecnico.',
                'technical' => $technical
            ];
        }
        return [
            'message' => 'Solicitudes del tecnico.',
            'request' => $request,
            'technical' =>  $technical
        ];
    }

    public function requestsClientId($root , array $args){
        $clientId = $args['id'];
        $client = Cliente_Interno::find($clientId);
        //dd($technical);
        if(!isset($client)){
            return [
                'message' => 'No existe cliente'
            ];
        }

        $technicaId = $client->id;
        $request = Solicitud::where('clientId',$clientId)
        ->orderBy('registrationDateTime','DESC')
        ->get();
        if(!isset($request)){
            return [
                'message' => 'No existe solicitudes del cliente.'
            ];
        }
        return [
            'message' => 'Solicitudes del clientes',
            'request' => $request,
            'client' =>  $client
        ];
    }

    public function listStatusPendingComplet($root,array $args){
        $requestData = $args['requestRequest'];
        $statusId = $requestData['id_status'];
        $technicianId = $requestData['id_technician'];
        if(is_null(Tecnico::find($technicianId))){
            return [
                'message' => 'No existe tecnico.'
            ];
        }
        
        $query = Solicitud::where('technicianId',$technicianId)
        ->orderBy('registrationDateTime','ASC');
        if(in_array($statusId,[1,2,3])){
            $query->where('stateId',$statusId);
        }
        $request = $query->get();
        return [
            'message'=>'Listado de solicitudes del tecnico.',
            'request' => $request
        ];
    }
}
