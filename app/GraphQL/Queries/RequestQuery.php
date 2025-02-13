<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Helpers\StatusHelper;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\Solicitud;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\StatusAssigner;
use App\Services\ValidationModels;

use function PHPUnit\Framework\isEmpty;

class RequestQuery
{
    public static  $entity_type = 'request';
    public static $stateRequestAccept = 2;
    public static $stateRequestCancel = 3;
    public static $stateRequestPeding = 1;

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
        $statusId = $requestData['id_status'] ?? StatusHelper::STATE_PENDING;
        $technicianId = $requestData['id_technician'];
        $dateFilter = $requestData['date_filter'] ?? StatusHelper::DATE_ALL; // Filtro de fecha: "hoy", "esta_semana", "este_mes", "todas"
        $orderFilter = $requestData['order_filter'] ?? StatusHelper::ORDER_BY_RECENT;  // Orden: "mas_recientes", "mas_antiguas"
/*
        if (is_null(Tecnico::find($technicianId))) {
            return [
                'message' => 'No existe técnico.'
            ];
        }*/
        ValidationModels::validationTechnician($technicianId);
        $stateId = StatusAssigner::allowState(self::$entity_type);

        // Consulta principal
        $query = Solicitud::where('requests.technicianId', $technicianId)
            ->join('internal_clients', 'requests.clientId', '=', 'internal_clients.id')
            ->join('users', 'internal_clients.userId', '=', 'users.id')
            ->join('state_types', 'requests.stateId', '=', 'state_types.id')
            ->select(
                'requests.*',
                'internal_clients.firstName',
                'internal_clients.lastName',
                'internal_clients.phoneNumber'
            )
            ->distinct();

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

    public function agenda_requests($root, $args){
    try {
            $now = Carbon::now()->format('Y-m-d');
            $parameter = $args['parameterSearch'];

            $clientId = $parameter['id_client'];
            $stateParameter = $parameter['stateId'] ?? null;
            $dateParameter = $parameter['visitDate'] ?? $now;

            // Verifica si existen solicitudes en la fecha dada
            if (!Solicitud::where('requests.clientId', $clientId)->whereDate('registrationDateTime', $dateParameter)->exists()) {
                return [
                    'message' => "No existen solicitudes en la agenda para: $dateParameter",
                    'status' => 2,
                    'request' => []
                ];
            }

            // Obtiene solicitudes por estado
            $requests = $this->getSolicitudesPorEstado($clientId, $stateParameter, $dateParameter);

            if ($requests->isEmpty()) {
                return [
                    'message' => "No existen solicitudes con el estado solicitado.",
                    'status' => 2,
                    'request' => []
                ];
            }

            return [
                'message' => "Solicitudes que son de la fecha: $dateParameter",
                'status' => 1,
                'request' => $requests
            ];
        } catch (\Exception $e) {
            return [
                'message' => "Surgió un problema en la consulta. " . $e->getMessage(),
                'status' => 3,
                'request' => null
            ];
        }
    }

    /**
     * Obtiene las solicitudes en base al estado proporcionado.
     */
    private function getSolicitudesPorEstado($clientId, $stateParameter, $dateParameter)
    {
        $query = Solicitud::join('technicians', 'requests.technicianId', '=', 'technicians.id')
            ->join('state_types', 'requests.stateId', '=', 'state_types.id')
            ->join('activity_types', 'requests.activityId', '=', 'activity_types.id')
            ->where('requests.clientId', $clientId)
            ->whereDate('requests.registrationDateTime', $dateParameter)
            ->select(
                'activity_types.description AS name_activity',
                'requests.titleRequests AS title',
                'requests.requestDescription AS description',
                DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) AS full_name'),
                'technicians.phoneNumber AS phoneNumber',
                'state_types.description AS stateAgenda'
            );

        switch ($stateParameter) {
            case self::$stateRequestPeding:
                $query->where('requests.stateId', self::$stateRequestPeding);
                break;

            case self::$stateRequestAccept:
                $query->join('services', 'services.requestsId', '=', 'requests.id')
                    ->where('requests.stateId', self::$stateRequestAccept);
                break;

            case self::$stateRequestCancel:
                $query->where('requests.stateId', self::$stateRequestCancel);
                break;

            default:
                return collect(); // Retorna colección vacía si el estado no es válido
        }

        return $query->distinct()->get()->map(function ($solict) {
            return [
                'activity_name' => $solict->name_activity,
                'title' => $solict->title,
                'description' => $solict->description,
                'full_name' => $solict->full_name,
                'phoneNumber' => $solict->phoneNumber,
                'stateAgenda' => $solict->stateAgenda
            ];
        });
    }

}
