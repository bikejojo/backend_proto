<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Helpers\StatusHelper;
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
        if(!isset($technical)){
            return [
                'message' => 'No existe tecnico'
            ];
        }

        $technicaId = $technical->id;
        $requests = Solicitud::where('technicianId',$technicaId)
        ->leftjoin('internal_clients','requests.clientId','=','internal_clients.id')
        ->leftjoin('users','internal_clients.userId','=','users.id')
        ->select('requests.*','internal_clients.firstName','internal_clients.lastName','internal_clients.phoneNumber','users.ci')
        ->orderBy('registrationDateTime','DESC')
        ->get();

        $_request = $requests->map(function ($request){
            return [
                'request' => $request,
                'client' => [
                    'fullName' => $request->firstName .' ' . $request->lastName,
                    'phoneNumber' => $request->phoneNumber,
                    'ci' => $request->ci
                ]
            ];
        });

        return [
            'message' => 'Solicitudes del tecnico.',
            'request' => $_request,
            'technical' =>  $technical
        ];
    }

    public function requestsClientId($root, array $args)
    {
        $clientId = $args['id'];

        // Obtener el cliente
        $client = Cliente_Interno::find($clientId);
        if (!$client) {
            return [
                'message' => 'No existe cliente'
            ];
        }

        // Obtener las solicitudes del cliente
        $requests = Solicitud::where('clientId', $clientId)
            ->leftjoin('technicians','requests.technicianId','=','technicians.id')
            ->leftjoin('users','technicians.userId','=','users.id')
            ->select('requests.*','technicians.firstName','technicians.lastName','technicians.phoneNumber','users.ci')// Asegúrate de cargar 'client' y 'user' en una sola consulta
            ->orderBy('registrationDateTime', 'DESC')
            ->get();

        //dd($requests);

        $_request = $requests->map(function ($request){
            return [
                'request' => $request,
                'technician' => [
                    'fullName' => $request->firstName .' ' . $request->lastName,
                    'phoneNumber' => $request->phoneNumber,
                    'ci' => $request->ci
                ]
            ];
        });

        return [
            'message' => 'Solicitudes del cliente.',
            'request' => $_request,
            'client' => $client
        ];
    }

    public function listStatusPendingComplet($root,array $args){
        $now=Carbon::now();
        $requestData = $args['requestRequest'];
        $statusId = $requestData['id_status'] ?? StatusHelper::STATE_PENDING;
        $technicianId = $requestData['id_technician'];
        $dateFilter = $requestData['date_filter'] ?? StatusHelper::DATE_ALL; // Filtro de fecha: "hoy", "esta_semana", "este_mes", "todas"
        $orderFilter = $requestData['order_filter'] ?? StatusHelper::ORDER_BY_RECENT;  // Orden: "mas_recientes", "mas_antiguas"
        //dd($orderFilter);
        if(is_null(Tecnico::find($technicianId))){
            return [
                'message' => 'No existe tecnico.'
            ];
        }
        $stateId = StatusAssigner::allowState(self::$entity_type);

        $query = Solicitud::where('technicianId',$technicianId)
        ->leftjoin('internal_clients','requests.clientId','=','internal_clients.id')
        ->leftjoin('users','internal_clients.userId','=','users.id')
        ->select('requests.*','internal_clients.firstName','internal_clients.lastName','internal_clients.phoneNumber','users.ci');

        if(in_array($statusId,$stateId)){
            $query->where('stateId',$statusId);
        }

        if($orderFilter){
            $query = StatusHelper::applyOrderFilter($query,$orderFilter);
        }
        //dd($query->get());
        if($dateFilter){
            $query = StatusHelper::applyDateFilter($query,$dateFilter);
        }

        $requests = $query->get();
        $_request = $requests->map(function ($request){
            return [
                'request' => $request,
                'client' => [
                    'fullName' => $request->firstName .' ' . $request->lastName,
                    'phoneNumber' => $request->phoneNumber,
                    'ci' => $request->ci
                ]
            ];
        });
        //dd($request);
        return [
            'message'=>'Listado de solicitudes del tecnico.',
            'request' => $_request,
            'technical' => Tecnico::find($technicianId)
        ];
    }
}
