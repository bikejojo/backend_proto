<?php

namespace App\GraphQL\Mutations;

use App\Events\ServicioAnulado;
use App\Events\SolicitudAceptada;
use App\Events\SolicitudCancelada;
use App\Models\Cliente_Interno;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Historial_Servicios;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Tecnico;
use App\Models\Lists_Internal_Client;
use App\Services\StateCatalog;
use App\Services\StatusAssigner;
use App\Events\SolicitudCreada;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\ValidationModels;

class SolicitudesMutations
{
    public static  $entity_type = StatusAssigner::ENTITY_REQUEST;
    protected $now;

    public function __construct()
    {
        $this->now = Carbon::now()->format('Y-m-d H:i:s');
    }

    public function createRequestClient($root,array $args){

            $requestData = $args['requestRequest'];
            $keys = array_keys($requestData);

            for($i=0;$i < count($requestData);$i++){
                $indexName = $keys[$i];
                if(empty($requestData[$indexName])){
                    return [
                        'message' => "El campo '{$indexName}' esta vacio"
                    ];
                }
            }

            $technicianId=$requestData['id_technician'];
            $clientId =$requestData['id_client'];
            $technician = ValidationModels::validationTechnician($technicianId);
            $client = ValidationModels::validationclientInternal($clientId);
            //dd($client);
            DB::beginTransaction();
            try{
                $request = new Solicitud();
                    $request->clientId = $client->id;
                    $request->technicianId = $technician->id;
                    $request->titleRequests = $requestData['titleRequests'];
                    $request->requestDescription = $requestData['requestDescription'];
                    $request->latitude = $requestData['latitude'];
                    $request->longitude = $requestData['longitude'];
                    $request->serviceLocation = $requestData['serviceLocation'];
                    $request->reference_phone = $requestData['reference_phone'];
                    $request->registrationDateTime = $this->now;
                    $request->status = StateCatalog::STATUS_ACTIVE;
                    $request->activityId = $requestData['id_activity'];
                $request->save();

                StatusAssigner::assignStateRequest($request,$this->now,self::$entity_type,'El cliente creo una solicitud nueva.',1);
                //$request->registrationDateTime = $this->now;
                $request->save();

                event(new SolicitudCreada($request));

                $historial = new Historial_Servicios();
                    $historial->clientId = $client->id;
                    $historial->technicianId = $technician->id;
                    $historial->jobId = $request->id;
                    $historial->descriptionJob = 1; // request
                    $historial->stateId = $request->stateId;
                    $historial->outsetDate = $request->registrationDateTime;
                    $historial->description = $request->requestDescription;
                $historial->save();

                $request = Solicitud::find($request->id);
                DB::commit();
                return [
                    'message' => 'Solicitud registrada',
                    'requests' => $request,
                    'client' => $client,
                    'technician' => $technician
                ];
            } catch(\Exception $e){
                DB::rollBack();
                return[
                    'message' => 'El error es.'. $e->getMessage()
                ];
            }
    }

    public function cancelRequestTechnician($root,array $args){
        // tipo 3
        $requestId = $args['id'];
        $request = Solicitud::find($requestId);
        $comments = 'Se cancelo la solicitud por el tecnico.';
        ###################################3
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        $cliente = Cliente_Interno::find($clientId);
        $tecnico = Tecnico::find($tecnicoId);
        StatusAssigner::assignStateRequest($request,$this->now,self::$entity_type,$comments,2);
        $_request = Solicitud::find($request->id);
        $request->save();

        event(new SolicitudCancelada($request));
        return[
            'message'=>'Solicitud rechazada por el tecnico',
            'requests'=>$_request,
            'client' => $cliente,
            'technician' => $tecnico
        ];
    }

    public function cancelRequestClient($root,array $args){

        try{
            $requestServ = $args['requestService'];
            $serviceId = $requestServ['servicesId'];
            $clientId = $requestServ['clientId'];
            $cliente = Cliente_Interno::find($clientId);
            $service = Servicio::where('services.id',$serviceId)
                        ->where('services.clientId',$cliente->id)
                        ->where('services.typeClient',1)
                        ->first();

            $technician = Tecnico::find($service->technicalId);
            if($service){
                StatusAssigner::assignStatService($service,$this->now,"services","Anulado por el cliente",7);
                    $service->stateId = 6;
                    $service->save();
                $request = Solicitud::find($service->requestsId);

                    $request->stateId = 6;
                    $request->save();
                    StatusAssigner::assignStateRequest($service,$this->now,"services","Anulado por el cliente",3);
            }else{
                DB::rollBack();
                return [
                    'message' => 'Surgio un problema al buscar la id de servicio.'
                ];
            }
            event(new ServicioAnulado($service,'services_anull_client'));
            event(new ServicioAnulado($service,'serv_anull_client'));
            DB::commit();
            return [
                'message'=>'Servicio Anulado completado',
                'client' => $cliente,
                'technician' => $technician,
                'service' => $service
            ];

        } catch(\Exception $e){
            DB::rollBack();
            return [
                'message' => 'Surgio un problema podidiov' . $e->getMessage()
            ];
        }
    }

    public function acceptRequest($root,array $args){
        $requestData = $args['requestRequest'];
        $requestId = $requestData['id_request'];
        $clientId = $requestData['id_client'];
        $tecnicoId=$requestData['id_technician'];
        $visitDateTime=$requestData['visitDateTime'];
        $comments = 'La solicitud fue aceptada por el tecnico.';
        $request = ValidationModels::validationRequest($requestId);
        $cliente = ValidationModels::validationclientInternal($clientId);
        $tecnico = ValidationModels::validationTechnician($tecnicoId);
        DB::beginTransaction();
        try{
            $existingServiceInternal = Servicio::where('services.technicalId',$tecnicoId)
            ->where('services.typeClient', '1')
            ->where('services.status', 1) // Solo servicios activos
            ->where('services.stateId', 1) // Solo servicios válidos
            ->where(function ($query) use ($visitDateTime){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($visitDateTime)->subMinutes(10), // 15 minutos antes
                    Carbon::parse($visitDateTime)->addMinutes(10)  // 15 minutos después
                ])
                ->Where('services.updatedDateTime','=',$visitDateTime);
            })->first();
            if($existingServiceInternal){
                DB::rollBack();
                return [
                    'message' => 'El horario seleccionado ya está ocupado. Por favor, elige otro disponible.'
                ];
            }
            StatusAssigner::assignStateRequest($request,$this->now,self::$entity_type,$comments,4);
            $request->save();
            $_request = Solicitud::find($request->id);
            $agenda = ValidationModels::validationAgenda($tecnico->id);
            $now= Carbon::now();

            $service = new Servicio();
                $service->requestsId =  $request->id;
                $service->technicalId = $tecnico->id;
                $service->clientId = $cliente->id;
                $service->activityId = $request->activityId;
                $service->typeClient = ServicioMutations::clientInternal;
                $service->service_origin = 1;  // cliente interno
                $service->titleService = $request->titleRequests;
                $service->serviceDescription = $request->requestDescription;
                $service->serviceLocation = $request->serviceLocation;
                $service->latitude = $request->latitude;
                $service->longitude = $request->longitude;
                $service->createdDateTime = $now;
                $service->updatedDateTime = $visitDateTime;
                $service->status = StateCatalog::STATUS_ACTIVE;
                $service->save();

            $_comments = 'Se creo un nuevo servicio por la solicitud ID'. $service->requestsId;
            StatusAssigner::assignStatService($service,$this->now,StatusAssigner::ENTITY_SERVICE,$_comments,1);
            $service->save();

            event(new SolicitudAceptada($service));

            $serviceId = $service->id;
            $agendaId = $agenda->id;

            $detail = new Detalle_Agenda_Tecnico();
                $detail->agendaTechnicalId = $agendaId;
                $detail->clientId = $cliente->id;
                $detail->serviceId = $serviceId;
                $detail->typeClient = $service->typeClient;
                $detail->service_origin = 1;
                $detail->serviceDate = $service->updatedDateTime;
                $detail->createDate = Carbon::now();
                $detail->save();

            $list = new Lists_Internal_Client();
                $list->technicianId = $tecnico->id;
                $list->clientId = $cliente->id;
                $list->typeClient = ServicioMutations::clientInternal;
                $list->requestsId = $_request->id;
                $list->save();

            $history=Historial_Servicios::where('jobId',$request->id)->where('descriptionJob',1)->first();
            $history->finishDate=$service->createdDateTime;
            $history->save();

            $historial = new Historial_Servicios();
                $historial->clientId = $cliente->id;
                $historial->technicianId = $tecnico->id;
                $historial->jobId = $service->id;
                $historial->descriptionJob = 2; // service
                $historial->outsetDate = $service->createdDateTime;
                $historial->description = 'El tecnico ha confirmado la solicitud';
                $historial->save();

            DB::commit();
            return[
                'message'=>'solicitud confirmada',
                'requests'=>$_request,
                'messageService' => 'Se agendara el servico en un momento',
                'service' => $service ,
                'agenda' => $detail ,
                'client' => $cliente,
                'technician' => $tecnico
            ];
        }catch(\Exception $e){
            DB::rollBack();
            return[
                'message' => 'Fallas en la aceptar la solicitud y crear el servicio '.$e->getMessage()
            ];
        }
    }
}
