<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\Tipo_Estado;
use App\Models\StateReference;
use Illuminate\Support\Facades\DB;
use app\Helpers\StatusHelper;
use app\Services\StatusAssigner;
use App\Services\ValidationModels;

use function PHPUnit\Framework\isEmpty;

class ServiceQuery
{
    #tipos de clientes
    const client_internal = 1;
    const client_external = 2;

    public function getExternalClient($root , array $args){
        $serviceData = $args['id'];
        $technician = ValidationModels::validationTechnician($serviceData);


        $service = Servicio::where('technicalId',$technician->id)
        ->where('typeClient',self::client_external)
        ->select('services.*','external_clients.*')
        ->leftjoin('external_clients','services.clientId','=','external_clients.id')
        ->orderBy('updatedDateTime','DESC')
        ->get();
        if(is_null($service)){
            return [
                'message' => 'no existe servicio.'
            ];
        }
        $servic = $service->map(function ($service) {
            $serviceAttributes = $service->getAttributes();
            return [
                '_service' => $serviceAttributes,
                'customer_external' => [
                    'fullName' => $service->fullName,
                    'phoneNumber' => $service->phoneNumber,
                ]
            ];
        });

        return [
            'message' => 'Listado de servicios de clientes externos',
            'service' => $servic,
            'technician' => $technician
        ];
    }

    public function getExternalClientEarring($root , array $args){
        $serviceData = $args['id'];
        $technician = ValidationModels::validationTechnician($serviceData);


        $service = Servicio::where('technicalId',$technician->id)
        ->where('typeClient',self::client_external)
        ->where('services.stateId', 1)
        ->where('state_reference.type','service')
        ->select('services.*','external_clients.*')
        ->leftjoin('external_clients','services.clientId','=','external_clients.id')
        ->leftjoin('state_reference','services.id','=','state_reference.serviceId')
        ->leftjoin('state_types','state_reference.stateId','=','state_types.id')
        ->orderBy('updatedDateTime','DESC')
        ->get();
        if($service->isEmpty()){
            return [
                'message' => 'no existen servicios en estado pendiente.'
            ];
        }
        $servic = $service->map(function ($service) {
            $serviceAttributes = $service->getAttributes();
            return [
                '_service' => $serviceAttributes,
                'customer_external' => [
                    'fullName' => $service->fullName,
                    'phoneNumber' => $service->phoneNumber,
                ]
            ];
        });

        return [
            'message' => 'Listado de servicios de clientes externos',
            'service' => $servic,
            'technician' => $technician
        ];
    }

    public function getExternalClientOver($root , array $args){
        $serviceData = $args['id'];
        $technician = ValidationModels::validationTechnician($serviceData);

        $service = Servicio::where('technicalId',$technician->id)
        ->where('typeClient',self::client_external)
        ->where('service.stateId',4)
        ->where('state_reference.type','service')
        ->select('services.*','external_clients.*')
        ->leftjoin('external_clients','services.clientId','=','external_clients.id')
        ->leftjoin('state_reference','services.id','=','state_reference.referenceId')
        ->leftjoin('state_types','state_reference.stateId','=','state_types.id')
        ->orderBy('updatedDateTime','DESC')
        ->get();

        if(isEmpty($service)){
            return [
                'message' => 'No existen servicios en estado completado.'
            ];
        }
        $servic = $service->map(function ($service) {
            $serviceAttributes = $service->getAttributes();
            return [
                '_service' => $serviceAttributes,
                'customer_external' => [
                    'fullName' => $service->fullName,
                    'phoneNumber' => $service->phoneNumber,
                ]
            ];
        });

        return [
            'message' => 'Listado de servicios de clientes externos',
            'service' => $servic,
            'technician' => $technician
        ];
    }


    public function getInternalClient($root , array $args){
        $serviceData = $args['id'];
        $technician = ValidationModels::validationTechnician($serviceData);

        $service = Servicio::where('technicalId',$technician->id)
        ->where('typeClient',self::client_internal)
        ->select('services.*','internal_clients.*')
        ->leftjoin('internal_clients','services.clientId','=','internal_clients.id')
        ->orderBy('updatedDateTime','DESC')
        ->get();
        if(is_null($service)){
            return [
                'message' => 'no existe servicio.'
            ];
        }
        $servic = $service->map(function ($service) {
            $serviceAttributes = $service->getAttributes();
            return [
                '_service' => $serviceAttributes,
                'customer_internal' => [
                    'firstName' => $service->firstName,
                    'lastName'=> $service->lastName,
                    'phoneNumber' => $service->phoneNumber,
                ]
            ];
        });

        return [
            'message' => 'Listado de servicios de clientes externos',
            'service_' => $servic,
            'technician' => $technician
        ];
    }

    public function getInternalClientEarring($root , array $args){
        $serviceData = $args['id'];
        $technician = ValidationModels::validationTechnician($serviceData);

        $service = Servicio::where('technicalId',$technician->id)
        ->where('typeClient',self::client_internal)
        ->where('service.stateId', 1)
        ->select('services.*','internal_clients.*')
        ->leftjoin('internal_clients','services.clientId','=','internal_clients.id')
        ->leftjoin('state_reference','services.id','=','state_reference.referenceId')
        ->leftjoin('state_types','state_reference.stateId','=','state_types.id')
        ->orderBy('updatedDateTime','DESC')
        ->get();
        if($service->isEmpty()){
            return [
                'message' => 'no existen servicios en estado pendiente.'
            ];
        }
        $servic = $service->map(function ($service) {
            $serviceAttributes = $service->getAttributes();
            return [
                '_service' => $serviceAttributes,
                'customer_internal' => [
                    'firstName' => $service->firstName,
                    'lastName' => $service->lastName,
                    'phoneNumber' => $service->phoneNumber,
                ]
            ];
        });

        return [
            'message' => 'Listado de servicios de clientes externos',
            'service' => $servic,
            'technician' => $technician
        ];
    }

    public function getInternalClientOver($root , array $args){
        $serviceData = $args['id'];
        $technician = ValidationModels::validationTechnician($serviceData);

        $service = Servicio::where('technicalId',$technician->id)
        ->where('typeClient',self::client_internal)
        ->where('service.stateId',4)
        ->select('services.*','internal_clients.*')
        ->leftjoin('internal_clients','services.clientId','=','internal_clients.id')
        ->leftjoin('state_reference','services.id','=','state_reference.referenceId')
        ->leftjoin('state_types','state_reference.stateId','=','state_types.id')
        ->orderBy('updatedDateTime','DESC')
        ->get();

        if(isEmpty($service)){
            return [
                'message' => 'No existen servicios en estado completado.'
            ];
        }
        $servic = $service->map(function ($service) {
            $serviceAttributes = $service->getAttributes();
            return [
                '_service' => $serviceAttributes,
                'customer_internal' => [
                    'firstName' => $service->firstName,
                    'lastName' => $service->lastName,
                    'phoneNumber' => $service->phoneNumber,
                ]
            ];
        });

        return [
            'message' => 'Listado de servicios de clientes externos',
            'service' => $servic,
            'technician' => $technician
        ];
    }

      ## Actividad que se realizo
      public function getInternalClientActivity($root , array $args){
        $serviceData = $args['requestService'];
        $technicianId = $serviceData['id_technician'];
        $activityId = $serviceData['id_activity'];
        $technician = ValidationModels::validationTechnician($technicianId);

        $query = Servicio::where('technicalId', $technician->id)
        ->where('typeClient', self::client_internal)
        ->leftJoin('internal_clients', 'services.clientId', '=', 'internal_clients.id')
        ->select('services.*', 'external_clients.fullName', 'external_clients.phoneNumber')
        ->orderBy('updatedDateTime', 'DESC');

        // Filtrar por actividad si se proporciona un activityId
        if (in_array($activityId, [1, 2, 3, 4])) {
            $query->where('activityId', $activityId);
        }

        $service = $query->get();
        if($service->isEmpty()){
            return [
                'message' => 'No existen servicios en esta actividad.'
            ];
        }
        $servic = $service->map(function ($service) {
            $serviceAttributes = $service->getAttributes();
            return [
                '_service' => $serviceAttributes,
                'customer_internal' => [
                    'firstName' => $service->firstName,
                    'lastName' => $service->lastName,
                    'phoneNumber' => $service->phoneNumber,
                ]
            ];
        });

        return [
            'message' => 'Listado de servicios de clientes externos',
            'service' => $servic,
            'technician' => $technician
        ];
    }

    public function technicianHistoryClientInternal($root, array $args){
        $historyData = $args['requestService'];
        $technicianId = $historyData['id_technician'];
        $clientId = $historyData['id_client'];

        // Validar técnico y cliente
        $technician = ValidationModels::validationTechnician($technicianId);
        $cliente = ValidationModels::validationclientInternal($clientId);

        // Obtener servicios con calificación
        $service = Servicio::leftJoin('rating', 'services.id', '=', 'rating.serviceId')
            ->where('services.technicalId', $technician->id)
            ->where('services.clientId', $cliente->id)
            ->where('services.typeClient', self::client_internal)
            ->select(
                'services.id AS service_id',
                'services.titleService',
                'services.serviceDescription',
                'services.serviceLocation',
                'services.technicalId',
                'services.clientId',
                'rating.id AS rating_id',
                'rating.rating',
                'rating.feedback'
            )
            ->get();
        //dd($service);
        return [
            'message' => 'Historial de servicios de un cliente',
            'service' => $service
        ];
    }


    public function technicianHistoryClientExternal($root, array $args){
        $historyData = $args['requestService'];
        $technicianId = $historyData['id_technician'];
        $clientId = $historyData['id_client'];
        $technician = ValidationModels::validationTechnician($technicianId);
        $cliente = ValidationModels::validationclientExternal($clientId);
        // Obtener servicios con calificación
        $service = Servicio::where('services.technicalId', $technician->id)
            ->where('services.clientId', $cliente->id)
            ->where('services.typeClient', self::client_external)
            ->select(
                'services.id AS service_id',
                'services.titleService',
                'services.serviceDescription',
                'services.serviceLocation',
                'services.longitude',
                'services.latitude',
                'services.technicalId',
                'services.clientId',
            )
            ->get();
        //dd($service);
        return [
            'message' => 'Historial de servicios de un cliente',
            'service' => $service
        ];
    }
}
