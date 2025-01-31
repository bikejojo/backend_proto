<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Servicio;
use Illuminate\Support\Facades\DB;
use app\Helpers\StatusHelper;
use App\Models\Tecnico;
use App\Models\Tecnico_Habilidad;
use App\Services\StateCatalog;
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
        $activityId = $historyData['id_activity'] ?? StateCatalog::CODE_ACT_ALL;

        // Validar técnico y cliente
        $technician = ValidationModels::validationTechnician($technicianId);
        $cliente = ValidationModels::validationclientInternal($clientId);

        // Obtener servicios con calificación
        $service_query = Servicio::leftJoin('rating', 'services.id', '=', 'rating.serviceId')
            ->where('services.technicalId', $technician->id)
            ->where('services.clientId', $cliente->id)
            ->where('services.typeClient', self::client_internal)
            ->select(
                'services.id AS service_id',
                'services.titleService',
                'services.serviceDescription',
                'services.serviceLocation',
                'services.updatedDateTime',
                'services.finishDateTime_client',
                'services.finishDateTime_technician',
                'services.technicalId',
                'services.clientId',
                'rating.id AS rating_id',
                'rating.rating',
                'rating.feedback',
                'services.activityId',
            );
            //->get();

        if($activityId != StateCatalog::CODE_ACT_ALL){
            $serv_query = $service_query->where('activityId',$activityId);
        }else{
            $serv_query = $service_query;
        }
        $service = $serv_query->get();
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
        $activityId = $historyData['id_activity'] ?? StateCatalog::CODE_ACT_ALL;
        $cliente = ValidationModels::validationclientExternal($clientId);
        // Obtener servicios con calificación
        $service_query = Servicio::where('services.technicalId', $technician->id)
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
                'services.updatedDateTime',
                'services.activityId',
            );

        if($activityId != StateCatalog::CODE_ACT_ALL){
            $serv_query = $service_query->where('activityId',$activityId);
        }else{
            $serv_query = $service_query;
        }
        $service = $serv_query->get();
        return [
            'message' => 'Historial de servicios de un cliente',
            'service' => $service
        ];
    }

    public function listsServiceClient($root , array $args){
        $clientId = $args['requestService']['id_client'];
        $cliente = ValidationModels::validationclientInternal($clientId);
        $service_query = Servicio::leftjoin('technicians','services.technicalId','=','technicians.id')
                            ->where('services.clientId',$cliente->id)
                            ->where('services.typeClient',self::client_internal)
                            ->where('services.status',1)
                            ->select('services.id',
                            'services.titleService',
                            'services.serviceDescription',
                            'services.serviceLocation',
                            'services.latitude',
                            'services.longitude',
                            'services.updatedDateTime',
                            'services.finishDateTime_technician',
                            'services.finishDateTime_client',
                            DB::raw('COALESCE("services"."stateId", 0) as id_state'),
                            'technicians.firstName',
                            'technicians.lastName',
                            'technicians.phoneNumber',
                            'technicians.photo',
            )->get();
        $count = Servicio::where('clientId',$cliente->id)
                ->where('typeClient',self::client_internal)
                ->where('status',1)
                ->count();

        $service = $service_query->map(function ($service) {
            return [
                'serviceContent' => [   'id' => $service->id,
                                        'titleService' => $service->titleService ,
                                        'serviceDescription' => $service->serviceDescription,
                                        'serviceLocation'=>$service->serviceLocation,
                                        'latitude'=>$service->latitude,
                                        'longitude'=>$service->longitude,
                                        'updatedDateTime'=>$service->updatedDateTime,
                                        'finishDateTime_technician'=>$service->finishDateTime_technician,
                                        'finishDateTime_client'=>$service->finishDateTime_client,
                                        'id_state' => isset($service->id_state) ? $service->id_state : 0,
                                ],
                'technician' => [
                                    'firstName' => $service->firstName,
                                    'lastName' => $service->lastName,
                                    'phoneNumber' => $service->phoneNumber,
                                    'photo' => $service->photo
                                ],
            ];
        });
        return [
            'message' => 'Listado de todos los servicios que el cliente hizo.!',
            'counter' => $count,
            'services' => $service
        ];
    }

    public function getAverageTechnical($root, array $args)
    {
        try {
            $cityId = $args['id_city'] ?? null;

            if (!$cityId) {
                return [
                    'message' => 'No existe ID de ciudad',
                    'status' => 2,
                    'technicianss' => []
                ];
            }

            // 🔹 Obtener la lista de técnicos destacados con promedio redondeado
            $listTechnician = Tecnico::selectRaw("ROUND(average_rating::NUMERIC, 2) as average_rating")
                ->addSelect('technicians.*') // Asegura que se incluyan todos los campos del técnico
                ->whereBetween('average_rating', [4.00, 5.00])
                ->where('cityId', $cityId)
                ->get();

            // 🔹 Obtener los IDs de técnicos para buscar sus habilidades
            $technicianIds = $listTechnician->pluck('id')->toArray();

            // 🔹 Obtener habilidades de los técnicos si existen
            $listSkill = collect(); // Inicia vacío
            if (!empty($technicianIds)) {
                $listSkill = Tecnico_Habilidad::join('skills', 'skills.id', '=', 'technician_skills.skillId')
                    ->whereIn('technician_skills.technicianId', $technicianIds)
                    ->select(
                        'technician_skills.technicianId as id_technician',
                        'technician_skills.experience',
                        'skills.name',
                        'skills.id as id_skills'
                    )
                    ->get();
            }

            // 🔹 Formatear la salida según GraphQL
            $content = $listTechnician->map(function ($technician) use ($listSkill) {
                // Filtrar las habilidades que pertenecen a este técnico
                $technicianSkills = $listSkill->where('id_technician', $technician->id)->values();

                return [
                    'technician' => [
                        'id'           => $technician->id,
                        'firstName'    => $technician->firstName,
                        'lastName'     => $technician->lastName,
                        'phoneNumber'  => $technician->phoneNumber,
                        'photo'        => $technician->photo,
                        'average_rating'   => $technician->average_rating
                    ],
                    'skillss' => $technicianSkills->map(function ($skill) {
                        return [
                            'id_technician' => $skill->id_technician ?? null,
                            'id_skillss'    => $skill->id_skills ?? null,
                            'name'          => $skill->name ?? null,
                            'experience'    => $skill->experience ?? null
                        ];
                    })->toArray()
                ];
            });

            return [
                'message' => 'Listado de técnicos destacados.',
                'status' => 1,
                'technicianss' => $content
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Error en la consulta: ' . $e->getMessage(),
                'status' => 3,
                'technicianss' => []
            ];
        }
    }

}
