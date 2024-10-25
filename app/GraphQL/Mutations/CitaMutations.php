<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Cita;
use App\Models\Agenda_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Cliente_Interno;

class CitaMutations{
    public function create($root, array $args){
        $citaData=$args['citaRequest'];
        $clientId = $citaData['id_client'];

        // verifica que tipo de cliente es este interno o externo
        if( Cliente_Interno::find($clientId) === null ){
            $typeClient = 1;
        }else{
            $typeClient = 2;
        }

        $cita=Cita::create([
            'technicianId' => $citaData['id_technician'],
            'clientId' => $citaData['id_client'],
            'serviceId' => $citaData['id_service'],
            'activityId' => $citaData['id_activity'],
            'typeClient' => $typeClient,
            'createdData' => Carbon::now(),
            'nextDate' => $citaData['nextData']
        ]);
        return [
            'message' => 'Nueva cita creada!',
            'citation' => $cita
        ];
    }
    public function update($root , array $args){
        $citaData=$args['citaRequest'];
        $citaId=$citaData['id_cita'];
        $clientId = $citaData['id_client'];
        if(Cliente_Interno::find($clientId)!=null){
            $typeClient=1;
        }else{
            $typeclient=2;
        }
        $cita=Cita::find($citaId);
        if( $citaData['id_activity' != 5]){
            $newCita = Cita::create([
                'technicialId' => $citaData['id_technicial'],
                'clientId' => $citaData['id_client'],
                'serviceId'=> $citaData['id_service'],
                'activityID'=>$citaData['id_activity'],
                'typeClient'=>$typeClient,
                'citationDescription'=>$citaData['citationDescription'],
                'nextDate'=>$citaData['nextDate'],
            ]);
            return [
                'message' =>'Nueva Cita Programada',
                'cita'=>$newCita
            ];
        }else{
            $cita->activityId = $citaData['id_activity'];
            $cita->finishedDate = Carbon::now();
            $cita->save();
            return [
                'message' =>'Nueva Cita Programada',
                'cita'=>$cita
            ];
        }
    }

}
