<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\Servicio;
use App\Models\Tipo_Estado; // Asegúrate de que este es el modelo correcto para la tabla de estados
use App\Models\StateReference;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatusAssigner{
    /**
     * Asignar un estado a una entidad específica (servicio o solicitud).
     *
     * @param  string  $entityType  Tipo de entidad ('solicitud' o 'servicio')
     * @param  int     $entityId    ID de la entidad (ID del servicio o solicitud)
     * @param  int     $stateId     ID del estado a asignar
     * @return bool
     */

    // Constantes para los estados de solicitud
    const REQUEST_PENDING = 'pendiente por aceptar';
    const REQUEST_REJECTED_T = 'rechazado por tecnico';
    const REQUEST_REJECTED_C = 'rechazado por cliente';
    const REQUEST_ACCEPTED_T = 'aceptado por tecnico';
    const REQUEST_ACCEPTED_C= 'aceptado por cliente';
    const REQUEST_FINISH = 'terminado';


    // Constantes para los estados de servicio
    const SERVICE_PENDING = 'pendiente';
    const SERVICE_COMPLETED = 'terminado';

    public static function assignStateRequest($objeto, $now, $type_reference ){

    }

    public static function aassignStateService($objeto, $now, $type_reference ){
    }
}
