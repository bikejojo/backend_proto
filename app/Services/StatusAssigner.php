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

    const PENDING  = 1;
    const REJECTED = 2;
    const ACCEPT   = 3;
    const FINISH   = 4;
    // Constantes para los estados de solicitud
    const REQUEST_PENDING = 'pendiente por aceptar.';
    const REQUEST_REJECTED_T = 'rechazado por tecnico.';
    const REQUEST_REJECTED_C = 'rechazado por cliente.';
    const REQUEST_ACCEPTED_T = 'aceptado por tecnico.';


    // Constantes para los estados de servicio
    const SERVICE_PENDING = 'pendiente por acabar.';
    const SERVICE_COMPLETED_C = 'termino el servicio por el lado del cliente.';
    const SERVICE_COMPLETED_T = 'se termino el servicio por el lado del tecnico.';

    public static function assignStateRequest($objeto, $now, $type_reference,$comments,$number ){
        switch ($number) {
            case 1:
                $stateReference = StateReference::create([
                    'referenceId' => $objeto->id,
                    'stateId' => self::PENDING,
                    'type' => $type_reference,
                    'descriptionState' => self::REQUEST_PENDING,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->id;
                $objeto->save();
            break;
            case 2:
                $stateReference = StateReference::create([
                    'referenceId' => $objeto->id,
                    'stateId' => self::REJECTED,
                    'type' => $type_reference,
                    'descriptionState' => self::REQUEST_REJECTED_T,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->id;
                $objeto->save();
            break;
            case 3:
                $stateReference = StateReference::create([
                    'referenceId' => $objeto->id,
                    'stateId' => self::REJECTED,
                    'type' => $type_reference,
                    'descriptionState' => self::REQUEST_REJECTED_C,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->id;
                $objeto->save();
            break;
            case 4:
                $stateReference = StateReference::create([
                    'referenceId' => $objeto->id,
                    'stateId' => self::ACCEPT,
                    'type' => $type_reference,
                    'descriptionState' => self::REQUEST_ACCEPTED_T,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->id;
                $objeto->save();
            break;
        }
    }

    public static function assignStatService($objeto, $type_reference, $now,$comments,$number ){
        switch ($number){
            case 1:
                $stateReference = StateReference::create([
                    'referenceId' => $objeto->id,
                    'stateId' => self::PENDING,
                    'type' => $type_reference,
                    'descriptionState' => self::SERVICE_PENDING,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->id;
                $objeto->save();
            break;
            case 2:
                $stateReference = StateReference::create([
                    'referenceId' => $objeto->id,
                    'stateId' => self::FINISH,
                    'type' => $type_reference,
                    'descriptionState' => self::SERVICE_COMPLETED_T,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->id;
                $objeto->save();
            break;
            case 3:
                $stateReference = StateReference::create([
                    'referenceId' => $objeto->id,
                    'stateId' => self::FINISH,
                    'type' => $type_reference,
                    'descriptionState' => self::SERVICE_COMPLETED_C,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->id;
                $objeto->save();
            break;
        }
    }
    public static function allowState($entity_type){
        try{
            $requestStates = ['Pendiente', 'Rechazado', 'Aceptado'];
            $serviceStates = ['Pendiente', 'Terminado'];

            if ($entity_type === 'request') {
                return Tipo_Estado::whereIn('description', $requestStates)
                    ->pluck('id')
                    ->toArray();
            }

            if ($entity_type === 'service') {
                return Tipo_Estado::whereIn('description', $serviceStates)
                    ->pluck('id')
                    ->toArray();
            }

            return [];
        } catch (\Exception $e){
                return ['message' => 'error.'.$e->getMessage()
            ];
        }
    }
}
