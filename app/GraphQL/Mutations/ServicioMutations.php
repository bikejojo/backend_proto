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
use App\Models\Tecnico;
use App\Services\StateCatalog;
use App\Services\StatusAssigner;
use App\Services\ValidationModels;

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

            $existingService = Servicio::where('services.technicalId',$technicalId->id)
            ->where('services.typeClient', self::clientInternal)
            ->where(function ($query) use ($serviceData){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($serviceData['updatedDateTime'])->subMinutes(15), // 15 minutos antes
                    Carbon::parse($serviceData['updatedDateTime'])->addMinutes(15)  // 15 minutos después
                ])
                ->orWhere('services.updatedDateTime','=', $serviceData['updatedDateTime']);
            })->first();

            if($existingService){
                DB::rollBack();
                return [
                    'message' => 'Existe un servicio registrado previamente, ponga un plazo mas largo en su hora.'
                ];
            }

            $service = Servicio::create([
                'technicalId' => $serviceData['id_technician'],
                'clientId' => $serviceData['id_client'],
                'activityId' => $serviceData['id_activity'],
                'typeClient' => self::clientInternal,
                'titleService' => trim($serviceData['titleService']),
                'serviceDescription' => trim($serviceData['serviceDescription']),
                'latitude' => isset($serviceData['latitude']) ? $serviceData['latitude'] : null,
                'longitude' => isset($serviceData['longitude']) ? $serviceData['longitude'] : null ,
                'serviceLocation' => isset($serviceData['serviceLocation']) ? $serviceData['serviceLocation'] : null,
                'createdDateTime' => $this->now,
                'updatedDateTime' => $serviceData['updatedDateTime'],
                'status' => StateCatalog::STATUS_ACTIVE
            ]);
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
            $detailAgenda = Detalle_Agenda_Tecnico::create([
                'agendaTechnicalId' => $agendaId,
                'clientId' => $serviceData['id_client'],
                'serviceId' => $_service->id,
                'typeClient' => self::clientInternal,
                'serviceDate' => $_service->updatedDateTime,
                'createDate' => Carbon::now()
            ]);
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
        //$clientId = ValidationModels::validationExternalCLient($serviceData['id_client']);
        //dd($clientId);
        $associant = Asociacion_Cliente_Tecnico::where('clientId',$serviceData['id_client'])
        ->where('technicalId',$serviceData['id_technician'])->first();
        if(is_null($associant)){
            return [
                'message' => 'No existe relacion entre tecnico y cliente externo.'
            ];
        }
        DB::beginTransaction();
        try{
            $existingService = Servicio::where('services.technicalId',$technicalId->id)
            ->where('services.typeClient', self::clientExternal)
            ->where(function ($query) use ($serviceData){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($serviceData['updatedDateTime'])->subMinutes(15), // 15 minutos antes
                    Carbon::parse($serviceData['updatedDateTime'])->addMinutes(15)  // 15 minutos después
                ])
                ->orWhere('services.updatedDateTime','=', $serviceData['updatedDateTime']);
            })->first();

            if($existingService){
                DB::rollBack();
                return [
                    'message' => 'Existe un servicio registrado previamente, ponga un plazo mas largo en su hora.'
                ];
            }

            $service = Servicio::create([
                'technicalId' => $serviceData['id_technician'],
                'clientId' => $serviceData['id_client'],
                'activityId' => $serviceData['id_activity'],
                'typeClient' => self::clientExternal,
                'titleService' => trim($serviceData['titleService']),
                'serviceDescription' => trim($serviceData['serviceDescription']),
                'latitude' => isset($serviceData['latitude']) ? $serviceData['latitude'] : null,
                'longitude' => isset($serviceData['longitude']) ? $serviceData['longitude'] : null ,
                'serviceLocation' => isset($serviceData['serviceLocation']) ? $serviceData['serviceLocation'] : null,
                'createdDateTime' => $now,
                'updatedDateTime' => $serviceData['updatedDateTime'],
                'status' => StateCatalog::STATUS_ACTIVE
            ]);
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
            $detailAgenda = Detalle_Agenda_Tecnico::create([
                'agendaTechnicalId' => $agendaId,
                'clientId' => $serviceData['id_client'],
                'serviceId' => $_service->id,
                'typeClient' => self::clientExternal,
                'serviceDate' => $_service->updatedDateTime,
                'createDate' => Carbon::now()
            ]);

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

        $service = ValidationModels::validationService($serviceId);

        DB::beginTransaction();
        try{
            $client = Cliente_Externo::find($service->clientId);
            $technician = Tecnico::find($service->technicalId);
            $service->finishDateTime_technician = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();
            $service->save();
            StatusAssigner::assignStatService($service,$this->now,self::$entity_type,'El servicio fue acabo para el cliente externo.',4);
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
            // Actualizar el estado a completado
            if($service->finishDateTime_client != null || $service->serviceDateTime_technician != null){
                StatusAssigner::assignStatService($service,$this->now,self::$entity_type,$comments,2);
                $service->save();
            }
            $_service = Servicio::find($service->id);
            $history=Historial_Servicios::where('jobId',$service->id)->where('descriptionJob',2)->first();
            $history->finishDate=$service->finishDateTime_client;
            $history->save();
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

    public function finishServiceTechnician($root,array $args){
        $serviceData = $args['requestService'];
        $serviceId = $serviceData['id_service'];
        $clientId = $serviceData['id_client'];
        $technicianId = $serviceData['id_tecnico'];
        $comments = $serviceData['comments'];
        $serviceDateTime = Carbon::parse($serviceData['finishDateTime_technician']);
        $service = ValidationModels::validationService($serviceId);

        $client = ValidationModels::validationclientInternal($clientId);

        $technician = ValidationModels::validationTechnician($technicianId);

        DB::beginTransaction();
        try {
            $service->finishDateTime_technician = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();
            $service->save();
            // Actualizar el estado a completado
            if($service->finishDateTime_client != null || $service->finishDateTime_technician != null){
                StatusAssigner::assignStatService($service,$this->now,self::$entity_type,$comments,2);
                $service->save();
            }
            $_service = Servicio::find($service->id);
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
        $serviceData = $args['requestService'];
        $serviceId=$serviceData['id_service'];
        $service = Servicio::find($serviceId);
        if($service != null){
            $service->status = StateCatalog::STATUS_LOW;
            $service->save();
            return[
                'message' => 'El servicio se elimino.',
                'service' => $service
            ];
        }
    }

    public function updateService ($root , array $args ){
        $serviceId = $args['id_service'];
        $service = ValidationModels::validationService($serviceId);
        $serviceData = $args['requestService'];
        DB::beginTransaction();
        try{
            $existingService = Servicio::where('services.technicalId',$service->technicalId)
            ->where('services.typeClient', $service->typeClient)
            ->where(function ($query) use ($serviceData){
                $query->whereBetween( 'services.updatedDateTime' , [
                    Carbon::parse($serviceData['updatedDateTime'])->subMinutes(15), // 15 minutos antes
                    Carbon::parse($serviceData['updatedDateTime'])->addMinutes(15)  // 15 minutos después
                ])
                ->orWhere('services.updatedDateTime','=', $serviceData['updatedDateTime']);
            })->first();

            if($existingService){
                DB::rollBack();
                return [
                    'message' => 'Existe un servicio registrado previamente, ponga un plazo mas largo en su hora.'
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
}
