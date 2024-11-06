<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Cliente_Interno;
use App\Models\Tecnico;
use App\Models\Solicitud;
use Carbon\Carbon;
use App\Services\StatusAssigner;

use function PHPUnit\Framework\isEmpty;

class RequestQuery
{
    public static  $entity_type = 'request';

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
        $now=Carbon::now();
        $requestData = $args['requestRequest'];
        $statusId = $requestData['id_status'];
        $technicianId = $requestData['id_technician'];
        if(is_null(Tecnico::find($technicianId))){
            return [
                'message' => 'No existe tecnico.'
            ];
        }
        $stateId = StatusAssigner::allowState(self::$entity_type);
        //dd($stateId);
        $query = Solicitud::where('technicianId',$technicianId)
        ->where('registrationDateTime','<',$now);
        if(in_array($statusId,$stateId)){
            $query->where('stateId',$statusId);
        }
        $request = $query->get();
        //dd($request);
        return [
            'message'=>'Listado de solicitudes del tecnico.',
            'request' => $request,
            'technical' => Tecnico::find($technicianId)
        ];
    }
}
