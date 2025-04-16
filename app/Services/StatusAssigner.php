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
    const COMPLT   = 5;
    const ANULAD   = 6;

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
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->id;
                    $stateReference->serviceId = null;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicianId;
                    $stateReference->stateId = self::PENDING;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = self::cliente_internal;
                    $stateReference->descriptionState = self::REQUEST_PENDING;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 2:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->id;
                    $stateReference->serviceId = null;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicianId;
                    $stateReference->stateId = self::REJECTED;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = self::cliente_internal;
                    $stateReference->descriptionState = self::REQUEST_REJECTED_T;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 3:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->id;
                    $stateReference->serviceId = null;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicianId;
                    $stateReference->stateId = self::ANULAD;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = self::cliente_internal;
                    $stateReference->descriptionState = self::REQUEST_REJECTED_C;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 4:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->id;
                    $stateReference->serviceId = null;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicianId;
                    $stateReference->stateId = self::ACCEPT;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = self::cliente_internal;
                    $stateReference->descriptionState = self::REQUEST_ACCEPTED_T;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 5:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->id;
                    $stateReference->serviceId = null;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicianId;
                    $stateReference->stateId = self::REJECTED;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = self::cliente_internal;
                    $stateReference->descriptionState = self::REQUEST_REJECTED_SYSTEM;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->status = 0;
                $objeto->save();
            break;
            case 6:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->id;
                    $stateReference->serviceId = null;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicianId;
                    $stateReference->stateId = self::COMPLT;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = self::cliente_internal;
                    $stateReference->descriptionState = self::SERVICE_COMPLETED_CI;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->status = 1;
                $objeto->save();
            break;
        }
    }

    public static function assignStatService($objeto, $now, $type_reference,$comments,$number ){
        switch ($number){
            case 1:
                $stateReference = new StateReference();
                    $stateReference->requestId = null;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::PENDING;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_PENDING;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 2:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->requestsId ?? null;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::FINISH;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_COMPLETED_T;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 3:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->requestsId;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::FINISH;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_COMPLETED_CI;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 4:
                $stateReference = new StateReference();
                    $stateReference->requestId = null;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::FINISH;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_COMPLETED_CE;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 5:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->requestsId;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::ANULAD;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_CANCEL;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 6:
                $servicioExistente = Servicio::find($objeto->id);
                    if (!$servicioExistente) {
                        throw new \Exception("El servicio con ID {$objeto->id} no existe.");
                    }
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->requestsId ?? null;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::COMPLT;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_COMPLETED_T;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
            case 7:
                $stateReference = new StateReference();
                    $stateReference->requestId = $objeto->requestsId;
                    $stateReference->serviceId = $objeto->id;
                    $stateReference->clientId = $objeto->clientId;
                    $stateReference->technicianId = $objeto->technicalId;
                    $stateReference->stateId = self::ANULAD;
                    $stateReference->type = $type_reference;
                    $stateReference->typeClient = $objeto->typeClient;
                    $stateReference->descriptionState = self::SERVICE_CANCEL;
                    $stateReference->observations = $comments;
                    $stateReference->dateCreate = $now;
                    $stateReference->save();
                $objeto->stateId = $stateReference->stateId;
                $objeto->save();
            break;
        }
    }
    public static function allowState($entity_type){
        try{
            $requestStates = ['Pendiente', 'Rechazado', 'Aceptado','Completado','Anulado','Terminado'];
            $serviceStates = ['Pendiente', 'Terminado','Completado','Anulado'];

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
