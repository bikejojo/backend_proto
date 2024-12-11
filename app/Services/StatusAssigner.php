<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\Servicio;
use App\Models\Tipo_Estado; // Asegúrate de que este es el modelo correcto para la tabla de estados
use App\Models\Tipo_Actividad;
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
    const REQUEST_REJECTED = 'rechazado por tecnico';
    const REQUEST_ACCEPTED = 'aceptado';
    const REQUEST_REJECTED_C = 'rechazado por cliente';

    // Constantes para los estados de servicio
    const SERVICE_PENDING = 'pendiente';
    const SERVICE_COMPLETED = 'terminado';

    public static function assignState($objeto, $state,$entity_type){
        try{
            $status = Tipo_Estado::where('entity_type',$entity_type)
            ->where('description',$state)
            ->first();
            if($entity_type === 'request'){
               $entity = Solicitud::find($objeto->id);
            }
            if($entity_type === 'service'){
                $entity = Servicio::find($objeto->id);
            }
            $entity->stateId = $status->id;
            return $entity->save();
        }
        catch (\Exception $e){
            return [
                'message'=> 'fallas en la inserccion.' . $e->getMessage()
            ];
        }
    }

    public static function allowState($entity_type){
        try{
            if($entity_type === 'request' ){
                $states = Tipo_Estado::where('entity_type',$entity_type)
                ->whereIn('description',[self::REQUEST_REJECTED,self::REQUEST_PENDING,self::REQUEST_ACCEPTED])
                ->pluck('id')
                ->toArray();
                return $states;
            }

            if($entity_type === 'service'){
                $states = Tipo_Estado::where('entity_type',$entity_type)
                ->whereIn('description',[self::SERVICE_PENDING,self::SERVICE_COMPLETED])
                ->pluck('id')
                ->toArray();
                return $states;
            }
        } catch (\Exception $e){
                return ['message' => 'error.'.$e->getMessage()
            ];
        }
    }
}
