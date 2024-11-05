<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\Servicio;
use App\Models\Tipo_Estado; // Asegúrate de que este es el modelo correcto para la tabla de estados

class StatusAssigner{
    // Constantes para los estados de solicitud
    const REQUEST_PENDING = 'pendiente por aceptar';
    const REQUEST_REJECTED = 'rechazado por tecnico';
    const REQUEST_ACCEPTED = 'aceptado';

    // Constantes para los estados de servicio
    const SERVICE_PENDING = 'pendiente';
    const SERVICE_COMPLETED = 'terminado';

    public static function assignRequest($request, $state){
        $validStates = [
            self::REQUEST_PENDING,
            self::REQUEST_REJECTED,
            self::REQUEST_ACCEPTED
        ];
        if(in_array($state,$validStates)){
            $status = Tipo_Estado::where('description',$state)->first();
            if($status){
                $request->stateId = $status->id;
                return $request->save();
            }
        }
        return false;
    }

    public static function assignService($service, $state){
        $validStates = [
            self::SERVICE_PENDING,
            self::SERVICE_COMPLETED
        ];
    }
}
