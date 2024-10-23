<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Cita;
use App\Models\Agenda_Tecnico;
use App\Models\Cliente_Interno;

class CitaMutations
{

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
        $clientId = $citaData['id_client'];
        // verifica que tipo de cliente es este interno o externo
        if( Cliente_Interno::find($clientId) === null ){
            $typeClient = 1;
        }else{
            $typeClient = 2;
        }

    }

}
