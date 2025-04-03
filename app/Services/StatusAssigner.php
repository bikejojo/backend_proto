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
    const REJECTED = 3;
    const ACCEPT   = 2;
    const FINISH   = 4;
    // Constantes para los estados de solicitud
    const REQUEST_PENDING = 'pendiente por aceptar.';
    const REQUEST_REJECTED_T = 'rechazado por tecnico.';
    const REQUEST_REJECTED_C = 'rechazado por cliente.';
    const REQUEST_REJECTED_SYSTEM = 'solicitud rechazada por tiempo de espera';
    const REQUEST_ACCEPTED_T = 'aceptado por tecnico.';

    const ENTITY_REQUEST = 'request';
    const ENTITY_SERVICE = 'service';

    // Constantes para los estados de servicio
    const SERVICE_PENDING = 'pendiente por acabar.';
    const SERVICE_COMPLETED_CI = 'servicio completado cliente interno.';
    const SERVICE_COMPLETED_CE = 'servicio completado cliente externo.';
    const SERVICE_COMPLETED_T = 'servicio completado tecnico.';
    const SERVICE_CANCEL = 'Servicio cancelado';

    // tipo de clientes
    const cliente_internal=1;
    const cliente_external=2;

    public static function assignStateRequest($objeto, $now, $type_reference,$comments,$number ){
        switch ($number) {
            case 1:

                $stateReference = StateReference::create([
                    //'referenceId' => $objeto->id,
                    'requestId' => $objeto->id,
                    'serviceId' => null,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicianId,
                    'stateId' => self::PENDING,
                    'type' => $type_reference,
                    'typeClient' => self::cliente_internal,
                    'descriptionState' => self::REQUEST_PENDING,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 2:
                $stateReference = StateReference::create([
                    'requestId' => $objeto->id,
                    'serviceId' => null,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicianId,
                    'stateId' => self::REJECTED,
                    'type' => $type_reference,
                    'typeClient' => self::cliente_internal,
                    'descriptionState' => self::REQUEST_REJECTED_T,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 3:
                $stateReference = StateReference::create([
                    'requestId' => $objeto->id,
                    'serviceId' => null,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicianId,
                    'stateId' => self::REJECTED,
                    'typeClient' => self::cliente_internal,
                    'type' => $type_reference,
                    'descriptionState' => self::REQUEST_REJECTED_C,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 4:
                $stateReference = StateReference::create([
                    'requestId' => $objeto->id,
                    'serviceId' => null,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicianId,
                    'stateId' => self::ACCEPT,
                    'type' => $type_reference,
                    'typeClient' => self::cliente_internal,
                    'descriptionState' => self::REQUEST_ACCEPTED_T,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 5:
                $stateReference = StateReference::create([
                    'requestId' => $objeto->id,
                    'serviceId' => null,
                    'clientId' => $objeto->clientId,
                    'technicianId' => null,
                    'stateId' => self::REJECTED,
                    'type' => $type_reference,
                    'typeClient' => self::cliente_internal,
                    'descriptionState' => self::REQUEST_REJECTED_SYSTEM,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->status = 0;
                $objeto->save();
            break;
        }
    }

    public static function assignStatService($objeto, $now, $type_reference,$comments,$number ){
        switch ($number){
            case 1:
                $stateReference = StateReference::create([
                    'requestId' => null,
                    'serviceId' => $objeto->id,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicalId,
                    'stateId' => self::PENDING,
                    'typeClient' => $objeto->typeClient,
                    'type' => $type_reference,
                    'descriptionState' => self::SERVICE_PENDING,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 2:
                $stateReference = StateReference::create([
                    'requestId' => null,
                    'serviceId' => $objeto->id,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicianId,
                    'stateId' => self::FINISH,
                    'type' => $type_reference,
                    'typeClient' => $objeto->typeClient,
                    'descriptionState' => self::SERVICE_COMPLETED_T,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 3:
                $stateReference = StateReference::create([
                    'requestId' => null,
                    'serviceId' => $objeto->id,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicalId,
                    'stateId' => self::FINISH,
                    'type' => $type_reference,
                    'typeClient' => $objeto->typeClient,
                    'descriptionState' => self::SERVICE_COMPLETED_CI,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 4:
                $stateReference = StateReference::create([
                    'requestId' => null,
                    'serviceId' => $objeto->id,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicalId,
                    'stateId' => self::FINISH,
                    'type' => $type_reference,
                    'typeClient' => $objeto->typeClient,
                    'descriptionState' => self::SERVICE_COMPLETED_CE,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 5:
                /*$stateReference = StateReference::create([
                    'requestId' => $objeto->requestsId,
                    'serviceId' => $objeto->id,
                    'clientId' => $objeto->clientId,
                    'technicianId' => $objeto->technicalId,
                    'stateId' => self::REJECTED,
                    'type' => $type_reference,
                    'typeClient' => $objeto->typeClient,
                    'descriptionState' => self::SERVICE_CANCEL,
                    'observations' => $comments,
                    'dateCreate' => $now
                ]);*/
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->requestsId;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::REJECTED;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_CANCEL;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                $objeto->stateId = $stateReference->stateId;
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
