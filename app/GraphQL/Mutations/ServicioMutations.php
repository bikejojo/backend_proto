<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Servicio;
use App\Models\Cliente_Externo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Agenda_Tecnico;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Historial_Servicios;
use App\Models\Solicitud;
use App\Models\Tecnico;
use App\Services\StateCatalog;
use App\Services\StatusAssigner;
use App\Services\ValidationModels;

use function PHPUnit\Framework\isEmpty;

class ServicioMutations
{
    public static $entity_type = "service";
    protected $now;

    public function __construct()
    {
        $this->now = Carbon::now();
    }
    //state
    const clientExternal= 2;
    const clientInternal= 1;
    // CREAR SERVICIO PARA CLIENTE INTERNO
    public function createInternal($root,array $args){
        $serviceData = $args['requestService'];
        $technicalId = ValidationModels::validationTechnician($serviceData['id_technician']);

        $clientId = ValidationModels::validationclientInternal($serviceData['id_client']);
        DB::beginTransaction();
        try{

            $existingServiceInternal = Servicio::where('services.technicalId',$technicalId->id)
            ->where('services.typeClient', self::clientInternal)
            ->where('services.status',1)
            ->where('services.stateId',1)
            ->where(function ($query) use ($serviceData){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($serviceData['updatedDateTime'])->subMinutes(10), // 15 minutos antes
                    Carbon::parse($serviceData['updatedDateTime'])->addMinutes(10)  // 15 minutos después
                ])
                ->where('services.updatedDateTime','=', $serviceData['updatedDateTime']);
            })->first();

            if($existingServiceInternal){
                DB::rollBack();
                return [
                    'message' => 'Este horario ya está ocupado en la agenda de cliente Interno, elige uno con más de 10 minutos de diferencia.'
                ];
            }
            $existingServiceExternal = Servicio::where('services.technicalId',$technicalId->id)
            ->where('services.typeClient', self::clientExternal)
            ->where('services.status',1)
            ->where('services.stateId',1)
            ->where(function ($query) use ($serviceData){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($serviceData['updatedDateTime'])->subMinutes(10), // 15 minutos antes
                    Carbon::parse($serviceData['updatedDateTime'])->addMinutes(10)  // 15 minutos después
                ])
                ->where('services.updatedDateTime','=', $serviceData['updatedDateTime']);
            })->first();

            if($existingServiceExternal){
                DB::rollBack();
                return [
                    'message' => 'Este horario ya está ocupado en la agenda de cliente Externo, elige uno con más de 10 minutos de diferencia.'
                ];
            }
            /*$service = Servicio::create([
                'technicalId' => $serviceData['id_technician'],
                'clientId' => $serviceData['id_client'],
                'activityId' => $serviceData['id_activity'],
                'typeClient' => self::clientInternal,
                'service_origin' => 2,
                'titleService' => trim($serviceData['titleService']),
                'serviceDescription' => trim($serviceData['serviceDescription']),
                'latitude' => isset($serviceData['latitude']) ? $serviceData['latitude'] : null,
                'longitude' => isset($serviceData['longitude']) ? $serviceData['longitude'] : null ,
                'serviceLocation' => isset($serviceData['serviceLocation']) ? $serviceData['serviceLocation'] : null,
                'createdDateTime' => $this->now,
                'updatedDateTime' => $serviceData['updatedDateTime'],
                'status' => StateCatalog::STATUS_ACTIVE
            ]);*/
            $service = new Servicio();
                $service->technicalId = $serviceData['id_technician'];
                $service->clientId = $serviceData['id_client'];
                $service->activityId = $serviceData['id_activity'];
                $service->typeClient = self::clientInternal;
                $service->service_origin = 2;
                $service->titleService = trim($serviceData['titleService']);
                $service->serviceDescription = trim($serviceData['serviceDescription']);
                $service->latitude = isset($serviceData['latitude']) ? $serviceData['latitude'] : null;
                $service->longitude = isset($serviceData['longitude']) ? $serviceData['longitude'] : null;
                $service->serviceLocation = isset($serviceData['serviceLocation']) ? $serviceData['serviceLocation'] : null;
                $service->createdDateTime = $this->now;
                $service->updatedDateTime = $serviceData['updatedDateTime'];
                $service->status = StateCatalog::STATUS_ACTIVE;
                $service->save();
            //dd($service);
            StatusAssigner::assignStatService($service,$this->now,self::$entity_type,'El servicio fue creado por el tecnico para cliente interno.',1);
            $_service = Servicio::find($service->id);
            $_service->save();
            $agenda = Agenda_Tecnico::where('technicianId',$technicalId->id)->first();
            if(!$agenda){
                $agenda = Agenda_Tecnico::create([
                    'technicianId' => $technicalId,
                    'createDate' => Carbon::now()
                ]);
            }

            $agendaId = $agenda->id;
            /*$detailAgenda = Detalle_Agenda_Tecnico::create([
                'agendaTechnicalId' => $agendaId,
                'clientId' => $serviceData['id_client'],
                'service_origin'=>2,
                'serviceId' => $_service->id,
                'typeClient' => self::clientInternal,
                'serviceDate' => $_service->updatedDateTime,
                'createDate' => Carbon::now()
            ]);*/
            $detailAgenda = new Detalle_Agenda_Tecnico();
                $detailAgenda->agendaTechnicalId = $agendaId;
                $detailAgenda->clientId = $serviceData['id_client'];
                $detailAgenda->service_origin = 2;
                $detailAgenda->serviceId = $_service->id;
                $detailAgenda->typeClient = self::clientInternal;
                $detailAgenda->serviceDate = $_service->updatedDateTime;
                $detailAgenda->createDate = Carbon::now();
                $detailAgenda->save();
            DB::commit();
            return[
                'message' => 'Servicio creado para cliente interno',
                'service' => $_service,
                'customer_internal' => $clientId,
                'technician' => $technicalId
            ];

        }catch(\Exception $e){
            DB::rollBack();
            return [
                'message' => 'Se presento un error en.' . $e->getMessage()
            ];
        }
    }
    // CREAR SERVICIO PARA CLIENTE EXTERNO
    public function createExternal($root,array $args){
        $serviceData = $args['requestService'];

        $now=Carbon::now();
        $technicalId = ValidationModels::validationTechnician($serviceData['id_technician']);
        $clientId = ValidationModels::validationclientExternal($serviceData['id_client']);

        $associant = Asociacion_Cliente_Tecnico::where('clientId',$serviceData['id_client'])
        ->where('technicalId',$serviceData['id_technician'])->first();
        if(is_null($associant)){
            return [
                'message' => 'No existe relacion entre tecnico y cliente externo.'
            ];
        }
        DB::beginTransaction();
        try{
            $existingServiceExternal = Servicio::where('services.technicalId', $technicalId->id)
                ->where('services.typeClient', self::clientExternal)
                ->where('services.status', 1) // Solo servicios activos
                ->where('services.stateId', 1) // Solo servicios válidos
                ->where(function ($query) use ($serviceData) {
                    $query->whereBetween('services.updatedDateTime', [
                        Carbon::parse($serviceData['updatedDateTime'])->subMinutes(10),
                        Carbon::parse($serviceData['updatedDateTime'])->addMinutes(10)
                    ])
                    ->where('services.updatedDateTime', '=', $serviceData['updatedDateTime']); // Coincidencia exacta
                })
                ->first();

            if($existingServiceExternal){
                DB::rollBack();
                return [
                    'message' => 'Este horario no está disponible  en la agenda de cliente externo. Selecciona otro con al menos 10 minutos de diferencia.'
                ];
            }

            $existingServiceInternal = Servicio::where('services.technicalId', $technicalId->id)
                ->where('services.typeClient', self::clientInternal)
                ->where('services.status', 1) // Solo servicios activos
                ->where('services.stateId', 1) // Solo servicios válidos
                ->where(function ($query) use ($serviceData) {
                    $query->whereBetween('services.updatedDateTime', [
                        Carbon::parse($serviceData['updatedDateTime'])->subMinutes(10),
                        Carbon::parse($serviceData['updatedDateTime'])->addMinutes(10)
                    ])
                    ->where('services.updatedDateTime', '=', $serviceData['updatedDateTime']); // Coincidencia exacta
                })
                ->first();

            if($existingServiceInternal){
                DB::rollBack();
                return [
                    'message' => 'Este horario no está disponible en la agenda de cliente interno. Selecciona otro con al menos 10 minutos de diferencia.'
                ];
            }

            $service = new Servicio();
                $service->technicalId = $serviceData['id_technician'];
                $service->clientId = $serviceData['id_client'];
                $service->activityId = $serviceData['id_activity'];
                $service->typeClient = self::clientExternal;
                $service->service_origin = 2;
                $service->titleService = trim($serviceData['titleService']);
                $service->serviceDescription = trim($serviceData['serviceDescription']);
                $service->latitude = $serviceData['latitude'] ?? null;
                $service->longitude = $serviceData['longitude'] ?? null;
                $service->serviceLocation = $serviceData['serviceLocation'] ?? null;
                $service->createdDateTime = $now;
                $service->updatedDateTime = $serviceData['updatedDateTime'];
                $service->status = StateCatalog::STATUS_ACTIVE;
                $service->save();

            StatusAssigner::assignStatService($service,$this->now,self::$entity_type,'El servicio fue creado por el tecnico para cliente externo.',1);
            $service->save();
            $_service = Servicio::find($service->id);
            $agenda = Agenda_Tecnico::where('technicianId',$technicalId->id)->first();

            if(!$agenda){
                $agenda = Agenda_Tecnico::create([
                    'technicianId' => $technicalId->id,
                    'createDate' => Carbon::now()
                ]);
            }

            $agendaId = $agenda->id;

            $detailAgenda = new Detalle_Agenda_Tecnico();
                $detailAgenda->agendaTechnicalId = $agendaId;
                $detailAgenda->clientId = $serviceData['id_client'];
                $detailAgenda->serviceId = $_service->id;
                $detailAgenda->typeClient = self::clientExternal;
                $detailAgenda->service_origin = 2;
                $detailAgenda->serviceDate = $_service->updatedDateTime;
                $detailAgenda->createDate = Carbon::now();
                $detailAgenda->save();

            DB::commit();
            return[
                'message'=>'Servicio registrado para cliente externo',
                'service'=>$_service,
                'customer_external' => $clientId
            ];
        }catch(\Exception $e){
            DB::rollBack();
            return [
                'message' => 'Fallas al momento de interactuar con la base de datos.' . $e->getMessage()
            ];
        }

    }
    public function finishServiceClientExternal($root,array $args){
        $serviceData = $args['requestService'];
        $serviceId = $serviceData['id_service'];
        $serviceDateTime = $serviceData['finishDateTime'];
        $service = ValidationModels::validationServiceExternal($serviceId);
        DB::beginTransaction();
        try{
            $client = DB::table('associationTechnClient')
                        ->join('external_clients', 'associationTechnClient.clientId', '=', 'external_clients.id')
                        ->where('associationTechnClient.clientId', $service->clientId)
                        ->where('associationTechnClient.technicalId', $service->technicalId)
                        ->select(
                            'external_clients.id',
                            'associationTechnClient.full_name as fullName',
                            'associationTechnClient.phone_number as phoneNumber'
                        )
                        ->first();
            $technician = Tecnico::find($service->technicalId);
            $service->finishDateTime_technician = $serviceDateTime;
            $service->save();
            StatusAssigner::assignStatService($service,$this->now,self::$entity_type,'El servicio fue acabo, para el cliente externo.',6);
            $service->save();
            $_service = Servicio::find($service->id);

            DB::commit();
            return [
                'message' => 'Servicio terminado',
                'customer_external' => $client ,
                'technician' => $technician ,
                'service' => $_service
            ];
        }catch(\Exception $e){
            DB::rollBack();
            return [
                'message' => 'Error en la actualizacion de datos.' . $e->getMessage()
            ];
        }
    }

    public function finishServiceClientInternal($root, array $args)
    {
        $serviceData = $args['requestService'];
        $serviceId = $serviceData['id_service'];
        $clientId = $serviceData['id_client'];
        $technicianId = $serviceData['id_tecnico'];
        $comments=$serviceData['comments'];
        $serviceDateTime = Carbon::parse($serviceData['finishDateTime']);

        $service = ValidationModels::validationService($serviceId);

        $client = ValidationModels::validationclientInternal($clientId);

        $technician = ValidationModels::validationTechnician($technicianId);

        DB::beginTransaction();
        try {
            $service->finishDateTime_client = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();
            $service->save();

            if($service->finishDateTime_client != null || $service->finishDateTime_technician != null){
                StatusAssigner::assignStatService($service,$this->now,self::$entity_type,$comments,3);
                $service->save();
            }
            $_service = Servicio::find($service->id);
            $history=Historial_Servicios::where('jobId',$service->id)->where('descriptionJob',2)->first();
            $history->finishDate=$service->finishDateTime_client;
            $history->save();
            //dd(is_null($_service->finishDateTime_technician));
            if(!is_null($_service->finishDateTime_technician)){
                $_service->stateId = 5;
                $_service->save();
            }

            DB::commit();

            return [
                'message' => 'Servicio terminado por el cliente.',
                'customer_internal' => $client,
                'technician' => $technician,
                'service' => $_service,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error en la actualización de datos: ' . $e->getMessage(),
            ];
        }
    }
// cliente interno
    public function finishServiceTechnician($root,array $args){
        $serviceData = $args['requestService'];
        $serviceId = $serviceData['id_service'];
        $clientId = $serviceData['id_client'];
        $technicianId = $serviceData['id_tecnico'];
        $comments = $serviceData['comments'] ?? '1';
        $serviceDateTime = Carbon::parse($serviceData['finishDateTime_technician']);
        $service = ValidationModels::validationService($serviceId);
        $client = ValidationModels::validationclientInternal($clientId);
        $technician = ValidationModels::validationTechnician($technicianId);
        $detailTech = Detalle_Agenda_Tecnico::where('serviceId',$service->id)->first();

        DB::beginTransaction();
        try {

            $detailTech->serviceDate = Carbon::now();
            $service->finishDateTime_technician = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();
            $service->save();
            if( !$service->finishDateTime_client || !$service->finishDateTime_technician ){
                StatusAssigner::assignStatService($service,$this->now,self::$entity_type,$comments,2);
                $service->save();
            }
            $_service = Servicio::find($service->id);

            /*if(!is_null($_service->finishDateTime_client)){
                $_service->stateId = 5;
                $_service->save();
            }*/

            /*if($_service->service_origin == 2){
                $_service->stateId = 5;
                $_service->save();
            }*/
            DB::commit();

            return [
                'message' => 'Servicio terminado por el técnico.',
                'customer_internal' => $client,
                'technician' => $technician,
                'service' => $_service,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error en la actualización de datos: ' . $e->getMessage(),
            ];
        }
    }

    public function delete($root , array $args){
        try {
            $serviceData = $args['requestService'];
            $serviceId=$serviceData['id_service'];
            $service = Servicio::find($serviceId);
            if($service != null){
                $request = Solicitud::where('id',$service->requestsId)->first();
                if($request){
                    $request->stateId = 6;
                    $request->save();
                }
                StatusAssigner::assignStatService($service,$this->now,self::$entity_type,'Se cancelo el servicio y la solicitud',5);
                $service->stateId = 6;
                $service->status = 1;
                $service->save();
                return[
                    'message' => 'El servicio se elimino.',
                    'service' => $service
                ];
            }
        } catch (\Exception $e) {
            return [
                'message' => 'surgio el problema' . $e->getMessage()
            ];
        }
    }

    public function updateService ($root , array $args ){
        $serviceId = $args['id_service'];
        $service = ValidationModels::validationService($serviceId);
        $serviceData = $args['requestService'];
        DB::beginTransaction();
        try{
            $existingServiceExternal = Servicio::where('services.technicalId',$service->technicalId)
            ->where('services.typeClient', $service->typeClient)
            ->where('services.status',1)
            ->where('services.stateId',1)

            ->where(function ($query) use ($serviceData){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($serviceData['updatedDateTime'])->subMinutes(10), // 15 minutos antes
                    Carbon::parse($serviceData['updatedDateTime'])->addMinutes(10)  // 15 minutos después
                ])
                ->where('services.updatedDateTime','=', $serviceData['updatedDateTime']);
            })->where('services.id','!=',$serviceId)
            ->first();

            if($existingServiceExternal){
                DB::rollBack();
                return [
                    'message' => 'Ya hay un servicio activo cerca de este horario. Selecciona una hora con al menos 10 minutos de diferencia.'
                ];
            }

            $service->titleService = $serviceData['titleService'] ?? $service->titleService;
            $service->serviceDescription= $serviceData['serviceDescription'] ?? $service->serviceDescription;
            $service->serviceLocation= $serviceData['serviceLocation'] ?? $service->serviceLocation;
            $service->longitude= $serviceData['longitude'] ?? $service->longitude;
            $service->latitude= $serviceData['latitude'] ?? $service->latitude;
            $service->activityId = $serviceData['id_activity'] ?? $service->activityId;
            $service->updatedDateTime = $serviceData['updatedDateTime'] ?? $service->updatedDateTime;
            $service->save();
            ######################
            $detail = Detalle_Agenda_Tecnico::where('serviceId',$service->id)->first();
            $detail->serviceDate = $service->updatedDateTime;
            $detail->save();
            ######################
            DB::commit();
            return [
                'message' => 'Servicio actualizado' ,
                'service' => $service
            ];
        }catch (\Exception $e){
            DB::rollback();
            return [
                'message' => 'Surgio un error al momento de actualizar el servicio' . $e->getMessage()
            ];
        }
    }
    public function verificationsServicio($root , array $args){
        try {
            $serviceData = $args['requestService'];
            $id_client = $serviceData['id_client'];

            $technician = ValidationModels::validation_clientInternal($id_client);

            // Consulta correctamente filtrada:
            $services = Servicio::join('technicians', 'services.technicalId', '=', 'technicians.id')
                ->join('internal_clients', 'services.clientId', '=', 'internal_clients.id')
                ->join('activity_types','services.activityId','=','activity_types.id')
                ->leftJoin('rating', function($join) {
                    $join->on('services.id', '=', 'rating.serviceId');
                })
                ->where('services.clientId', $technician->id)
                ->where('services.stateId', 4)                  // solo estado = 5
                ->whereNull('services.finishDateTime_client')   // fecha cliente vacía (null)
                ->whereNull('rating.id')
                ->select([
                    'technicians.id as id_technician',
                    DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) AS "fullName_technician"'),
                    'technicians.photo as photo_technician',
                    'internal_clients.id as id_client',
                    DB::raw('CONCAT(COALESCE(internal_clients."firstName", \'\'), \' \', COALESCE(internal_clients."lastName", \'\')) AS "fullName_client"'),
                    'internal_clients.photo as photo_client',
                    'services.id as id_service',
                    'services.titleService as title_service',
                    'activity_types.description As activity_service',
                    'services.serviceDescription as description_service'
                ])
                ->get();
                //dd($services);
            if($services->isEmpty()){
                return [
                    'message' => 'Existen comentario',
                    'status' => false
                ];
            }

            return [
                'message' => 'Servicios encontrados correctamente.',
                'status' => true,
                'services' => $services
            ];

        } catch(\Exception $e) {
            return [
                'message' => 'Surgieron las siguientes fallas: ' . $e->getMessage(),
                'status' => false
            ];
        }
    }

}
