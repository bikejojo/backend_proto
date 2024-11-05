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
        $technicianId=$requestData['id_technician'];
        $clientId =$requestData['id_client'];

        $client=Cliente_Interno::find($clientId);
        if(!$client){
            return [ 'message' => 'Cliente no encontrado.'];
        }

        $technician=Tecnico::find($technicianId);
        if(!$technician){
            return [ 'message' => 'Tecnico no encontrado.'];
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
        }elseif($state === $accept ){
            $request->stateId = $state;
            $request->status= 1;
            $request->save();
            return[
                'message'=>'solicitud confirmada',
                'requests'=>$request,
                'messageService' => 'Se agendara el servico en un momento',
                'client' => $cliente,
                'technician' => $tecnico
            ];
        }

    }

    public function cancelRequest($root,array $args){
        // tipo 2
        $requestData = $args['requestRequest'];
        $requestId = $requestData['id_requests'];
        $state = $requestData['id_state'];
        $request = Solicitud::find($requestId);
        ###################################3
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        $cliente = Cliente_Interno::find($clientId);
        $tecnico = Tecnico::find($tecnicoId);
        $request->stateId = $state;
        $request->status= 1;
        $request->save();
        return[
            'message'=>'Solicitud rechazada por el tecnico',
            'requests'=>$request,
            'client' => $cliente,
            'technician' => $tecnico
        ];
    }

    public function acceptRequest($root,array $args){
        // tipo 3
        $requestData = $args['requestRequest'];
        $requestId = $requestData['id_requests'];
        $state = $requestData['id_state'];
        $request = Solicitud::find($requestId);
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        $cliente = Cliente_Interno::find($clientId);
        $tecnico = Tecnico::find($tecnicoId);
        $request->stateId = $state;
        $request->status= 1;
        $request->save();
        return[
            'message'=>'solicitud confirmada',
            'requests'=>$request,
            'messageService' => 'Se agendara el servico en un momento',
            'client' => $cliente,
            'technician' => $tecnico
        ];
    }
}
