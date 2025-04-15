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
    public static $entity_type = 'request';
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

        ValidationModels::validationTechnician($technicianId);
        $stateId = StatusAssigner::allowState(self::$entity_type);

        // Consulta principal
        $query = Solicitud::where('requests.technicianId', $technicianId)
            ->join('internal_clients', 'requests.clientId', '=', 'internal_clients.id')
            //->join('users', 'internal_clients.userId', '=', 'users.id')
            //->join('state_types', 'requests.stateId', '=', 'state_types.id')
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
        //dd($requests);
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
 // agenda cliente aPP
    public function agenda_requests($root, $args){
    try {
            $now = Carbon::now()->format('Y-m-d');
            $parameter = $args['parameterSearch'];

            $clientId = $parameter['id_client'];
            $dateParameter = $parameter['visitDate'] ?? $now;

            $requests = $this->getSolicitudesPorEstado($clientId,  $dateParameter);
            //dd($requests);
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
    private function getSolicitudesPorEstado($clientId,  $dateParameter)
    {

        $query = DB::table('services as s')
                    ->select(
                        's.id as service_id',
                        's.titleService as service_title',
                        's.serviceDescription as service_description',
                        's.stateId as service_state',
                        's.updatedDateTime as relevant_date',
                        DB::raw('CONCAT(COALESCE(t."firstName", \'\'), \' \', COALESCE(t."lastName", \'\')) AS full_name'),
                        't.phoneNumber AS phoneNumber',
                        'at.description AS name_activity',
                    )
                    ->join('technicians as t', 's.technicalId', '=', 't.id')
                    ->join('activity_types as at', 's.activityId', '=', 'at.id')
                    ->where('s.clientId', $clientId)
                    ->where('s.typeClient',1) // cliente internos
                    ->whereDate('s.updatedDateTime', $dateParameter)
                    ->orderBy('s.updatedDateTime', 'asc');

        return $query->distinct()->get()->map(function ($solict){
            return [
                'full_name'=>$solict->full_name,
                'phoneNumber'=>$solict->phoneNumber,
                'id_services'=>$solict->service_id,
                'title'=>$solict->service_title,
                'description'=>$solict->service_description,
                'activity_name'=>$solict->name_activity,
                'state_services'=>$solict->service_state,
                'datetime'=>$solict->relevant_date,
            ];
        });
    }

    public function getRequestData($root,array $args){
        try{
            $conten = [

                'requestPending' => Solicitud::where('stateId',1)->count(),
                'requestAccept'  => Solicitud::where('stateId',2)->count(),
                'requestCancel'  => Solicitud::where('stateId',3)->count(),
            ];

            return [
                'message' => 'conteo exitoso de las solicitudes',
                'requestAll'     => Solicitud::count(),
                'conteo' => $conten
            ];

        }catch(\Exception $e){
            return [
                'message' => 'Se presentaron las siguientes fallas:' . $e->getMessage()
            ];
        }
    }
}
