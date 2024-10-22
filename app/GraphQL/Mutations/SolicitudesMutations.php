<?php

namespace App\GraphQL\Mutations;

use App\Models\Cita;
use App\Models\Cliente_Interno;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Tecnico;
use Carbon\Carbon;

class SolicitudesMutations
{
    public function create($root , array $args){
        $requestData = $args['requestRequest'];
        $description = trim($requestData['requestDescription']);
        $location = trim($requestData['locationDescription']);
        $now=Carbon::now();
        $clientId =$requestData['id_client'];
        if($clientId->isEmpity()){
            $client=Cliente_Interno::find($clientId);
        }
        $technicianId=$requestData['id_technican'];
        if($technicianId->isEmpity()){
            $technician=Tecnico::find($technicianId);
        }
        ##########################
        $request = new Solicitud();
        $request->clientId = $requestData['id_client'];
        $request->technicianId = $requestData['id_technician'];
        $request->stateId = $requestData['id_state'];
        $request->requestDescription = $description;
        $request->latitude = $requestData['latitude'];
        $request->longitude = $requestData['latitude'];
        $request->locationDescription = $location;
        $request->registationDateTime = $now;
        $request->expirationDateTime=$now->addMinutes(10);
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
        $converciotion = 3;
        $rejectTime = 4;
        $rejectTechn=5;
        $accepted=6;
        $client = 1;
        $process = 7;
        $requestData = $args['requestRequest'];
        $requestId = $requestData['id_request'];
        $request = Solicitud::find($requestId);
        $state = $request->stateId;
        switch($state){
            case 3:
                $request->stateId =$converciotion;
                $request->updateDateTime = Carbon::now();
                break;
            case 4:
                $request->stateId =$rejectTime;
                $request->updateDateTime = Carbon::now();
                break;
            case 5:
                $request->stateId =$rejectTechn;
                $request->updateDateTime = Carbon::now();
                break;
            case 6:
                $request->stateId =$accepted;
                $program=$requestData['programDate'];
                $request->updateDateTime = Carbon::now();
                break;
        }
        $request->save();
        if($request->stateId == 6){
            $service = new Servicio();
            $service->techinicalId=$request->technicianId;
            $service->clientId=$request->clientId;
            $service->typeClient=$client;
            $service->stateId=$process;
            $service->requestId=$request->id;
            $service->programDate = $program;
            $service->serviceDescription=$request->requestDescription;
            $service->requestDate = Carbon::now();
            $service->save();

            return[
                'messageRequest'=>'solicitud confirmada',
                'request'=>$request,
                'messageService' => 'Servicio en proceso',
                'service' => $service
            ];
        }else{
            return[
                'message'=>'solicitud modificada',
                'request'=>$request
            ];
        }
    }

    private function errorResponse($message){
        return [
            'message' => $message,
            'solicitud' => null
        ];
    }
}
