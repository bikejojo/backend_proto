<?php

namespace App\GraphQL\Mutations;

use App\Models\Cliente_Interno;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Historial_Servicios;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Tecnico;
use App\Models\Lists_Internal_Client;
use App\Services\StateCatalog;
use App\Services\StatusAssigner;

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
        $technicianId=$requestData['id_technician'];
        $clientId =$requestData['id_client'];
        $technician = ValidationModels::validationTechnician($technicianId);
        $client = ValidationModels::validationclientInternal($clientId);
        DB::beginTransaction();
        try{
            $request = Solicitud::create([
                'clientId' => $client->id,
                'technicianId' => $technician->id,
                'titleRequests'=> $requestData['titleRequests'],
                'requestDescription'=>$requestData['requestDescription'],
                'latitude'=>$requestData['latitude'],
                'longitude'=>$requestData['longitude'],
                'serviceLocation'=>$requestData['serviceLocation'],
                'reference_phone'=>$requestData['reference_phone'],
                'status'=>StateCatalog::STATUS_ACTIVE,
                'activityId' => $requestData['id_activity']
            ]);
            StatusAssigner::assignStateRequest($request,$this->now,self::$entity_type,'El cliente creo una solicitud nueva.',1);
            $request->registrationDateTime = $this->now;
            $request->save();

            $historial = Historial_Servicios::create([
                'clientId' => $client->id,
                'technicianId' => $technician->id,
                'jobId'=>$request->id,
                'descriptionJob'=>1, // request
                'stateId'=> $request->stateId,
                'outsetDate'=>$request->registrationDateTime,
                'description'=>$request->requestDescription
            ]);

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
        return[
            'message'=>'Solicitud rechazada por el tecnico',
            'requests'=>$_request,
            'client' => $cliente,
            'technician' => $tecnico
        ];
    }

    public function cancelRequestClient($root,array $args){
        // tipo 2
        $requestId = $args['id'];
        $comments = 'La solicitud fue cancelada por el cliente.';
        $request = ValidationModels::validationRequest($requestId);
        ###################################3
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        $cliente = Cliente_Interno::find($clientId);
        $tecnico = Tecnico::find($tecnicoId);
        StatusAssigner::assignStateRequest($request,$this->now,self::$entity_type,$comments,3);
        $_request = Solicitud::find($request->id);
        $request->save();
        return[
            'message'=>'Solicitud rechazada por el cliente',
            'requests'=>$_request,
            'client' => $cliente,
            'technician' => $tecnico
        ];
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
            $existingService = Servicio::where('services.technicalId',$tecnicoId)
            ->where('services.typeClient', '1')
            ->where(function ($query) use ($visitDateTime){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($visitDateTime)->subMinutes(15), // 15 minutos antes
                    Carbon::parse($visitDateTime)->addMinutes(15)  // 15 minutos después
                ])
                ->orWhere('services.updatedDateTime','=',$visitDateTime);
            })->first();
            if($existingService){
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
            $service = Servicio::create([
                'requestsId' => $request->id,
                'technicalId' => $tecnico->id,
                'clientId' => $cliente->id,
                'activityId' => $request->activityId,
                'typeClient' => ServicioMutations::clientInternal,
                'titleService' => $request->titleRequests,
                'serviceDescription' => $request->requestDescription,
                'serviceLocation' => $request->serviceLocation,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'createdDateTime' => $now,
                'updatedDateTime' => $visitDateTime,
                'status' => StateCatalog::STATUS_ACTIVE
            ]);
            $_comments = 'Se creo un nuevo servicio por la solicitud recien creada.';
            StatusAssigner::assignStatService($service,$this->now,StatusAssigner::ENTITY_SERVICE,$_comments,1);
            $service->save();

            $serviceId = $service->id;
            $agendaId = $agenda->id;
            $detail = Detalle_Agenda_Tecnico::create([
                'agendaTechnicalId' => $agendaId,
                'clientId' => $cliente->id,
                'serviceId' => $serviceId,
                'typeClient' => $service->typeClient,
                'serviceDate' => $service->updatedDateTime,
                'createDate' => Carbon::now()
            ]);

            $list= Lists_Internal_Client::create([
                'technicianId'=> $tecnico->id,
                'clientId'=> $cliente->id,
                'typeClient'=> ServicioMutations::clientInternal,
                'requestsId'=> $_request->id,
            ]);

            $history=Historial_Servicios::where('jobId',$request->id)->where('descriptionJob',1)->first();
            $history->finishDate=$service->createdDateTime;
            $history->save();

            $historial=Historial_Servicios::create([
                'clientId' => $cliente->id,
                'technicianId' => $tecnico->id,
                'jobId'=>$service->id,
                'descriptionJob'=>2, //service
                'outsetDate'=>$service->createdDateTime,
                'description'=>'El tecnico ha confirmado la solicitud'
            ]);

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
