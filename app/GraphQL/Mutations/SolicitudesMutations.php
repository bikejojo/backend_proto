<?php

namespace App\GraphQL\Mutations;

use App\Models\Cita;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Agenda_Tecnico;
use App\Models\Cliente_Interno;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Tecnico;
use Carbon\Carbon;

class SolicitudesMutations
{
    public function create($root , array $args){
        $requestData = $args['requestRequest'];
        $stateValue = 1;

        $description = trim($requestData['requestDescription']);
        $clientId =$requestData['id_client'];
        if($clientId!== null){
            $client=Cliente_Interno::find($clientId);
        }
        //dd($client);
        $technicianId=$requestData['id_technician'];
        if($technicianId!==null){
            $technician=Tecnico::find($technicianId);
        }
        ##########################
        $request = new Solicitud();

        $request->clientId = $client->id;
        $request->technicianId = $technician->id;
        $request->stateId = $requestData['id_state'];
        $request->requestDescription = $description;
        $request->status = $stateValue;
        $request->save();
        ##########################
        return [
            'message' => 'Solicitud registrada',
            'requests' => $request,
            'client' => $client,
            'technician' => $technician
        ];
   }
    public function modifyState($root,array $args){
        $reject = 2;
        $accept = 3;

        $requestData = $args['requestRequest'];
        $requestId = $requestData['id_requests'];
        $state = $requestData['id_state'];
        $request = Solicitud::find($requestId);
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        $cliente = Cliente_Interno::find($clientId);
        $tecnico = Tecnico::find($tecnicoId);
        if($state === $reject){
            $request->stateId = $state;
            $request->status= 0;
            $request->save();
            return[
                'message'=>'Solicitud rechazada por el tecnico',
                'requests'=>$request,
                'client' => $cliente,
                'technician' => $tecnico
            ];
        }

        $request->stateId = $state;
        $request->save();

        $service = new Servicio();

        $service->save();

        $tecnicoId=$tecnico->id;
        //dd($cliente->id);

        $agenda=Agenda_Tecnico::where('technicianId',$tecnicoId)->first();

        $detalleAgenda=Detalle_Agenda_Tecnico::create([

        ]);
        return[
            'message'=>'solicitud confirmada',
            'requests'=>$request,
            'messageService' => 'Servicio en proceso',
            'service' => $service,
            'client' => $cliente,
            'technician' => $tecnico
        ];

    }
}
