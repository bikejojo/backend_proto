<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Helpers\StatusHelper;
use App\Models\Cliente_Interno;
use App\Models\Tecnico;
use App\Models\Solicitud;
use Carbon\Carbon;
use App\Services\StatusAssigner;
use App\Services\ValidationModels;

class RequestQuery
{
    public static  $entity_type = 'request';

    public function requestsTechnicalId($root , array $args){
        $technicalId = $args['id'];
        $technical = ValidationModels::validationTechnician($technicalId);

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
        $client = ValidationModels::validationclientInternal($clientId);
        // Obtener las solicitudes del cliente
        $requests = Solicitud::where('clientId', $clientId)
            ->leftjoin('technicians','requests.technicianId','=','technicians.id')
            ->leftjoin('users','technicians.userId','=','users.id')
            ->select('requests.*','technicians.firstName','technicians.lastName','technicians.phoneNumber','users.ci')// Asegúrate de cargar 'client' y 'user' en una sola consulta
            ->orderBy('registrationDateTime', 'DESC')
            ->get();


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

    public function listStatusPendingComplet($root, array $args)
    {
        $now = Carbon::now();
        $requestData = $args['requestRequest'];
        $statusId = $requestData['id_state'] ?? StatusHelper::STATE_PENDING;
        $technicianId = $requestData['id_technician'];
        $dateFilter = $requestData['date_filter'] ?? StatusHelper::DATE_ALL; // Filtro de fecha: "hoy", "esta_semana", "este_mes", "todas"
        $orderFilter = $requestData['order_filter'] ?? StatusHelper::ORDER_BY_RECENT;  // Orden: "mas_recientes", "mas_antiguas"

        if (is_null(Tecnico::find($technicianId))) {
            return [
                'message' => 'No existe técnico.'
            ];
        }

        $stateId = StatusAssigner::allowState(self::$entity_type);

        // Consulta principal
        $query = Solicitud::where('requests.technicianId', $technicianId)
            ->where('state_reference.type', 'request')
            ->leftjoin('internal_clients', 'requests.clientId', '=', 'internal_clients.id')
            ->leftjoin('users', 'internal_clients.userId', '=', 'users.id')
            ->leftjoin('state_reference', 'state_reference.requestId', '=', 'requests.id')
            ->leftjoin('state_types', 'requests.stateId', '=', 'state_types.id')
            ->select(
                'requests.*',
                'state_types.id as state_type_id', // Alias para evitar colisiones con id_state en la tabla requests
                'internal_clients.firstName',
                'internal_clients.lastName',
                'internal_clients.phoneNumber',
                'users.ci'
            );

        if (in_array($statusId, $stateId)) {
            $query->where('requests.stateId', $statusId);
        }

        if ($orderFilter) {
            $query = StatusHelper::applyOrderFilter($query, $orderFilter);
        }

        if ($dateFilter) {
            $query = StatusHelper::applyDateFilter($query, $dateFilter);
        }

        // Obtén los resultados de la consulta
        $requests = $query->get();

        // Mapea los resultados para cumplir con el esquema _requesttechnician
        $_request = $requests->map(function ($request) {
            return [
                /*'request' => [
                    'id' => $request->id,
                    'id_client' => $request->clientId,
                    'id_technician' => $request->technicianId,
                    'id_state' => $request->state_type_id, // Sobrescribe con el valor de state_types.id
                    'id_activity' => $request->id_activity,
                    'titleRequests' => $request->titleRequests,
                    'requestDescription' => $request->requestDescription,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'reference_phone' => $request->reference_phone,
                    'status' => $request->status,
                    'registrationDateTime' => $request->registrationDateTime,
                ]*/
                'request' => $request,
                'client' => [
                    'fullName' => $request->firstName . ' ' . $request->lastName,
                    'phoneNumber' => $request->phoneNumber,
                ],
            ];
        });

        return [
            'message' => 'Listado de solicitudes del técnico.',
            'request' => $_request,
            'technical' => Tecnico::find($technicianId),
        ];
    }


}
