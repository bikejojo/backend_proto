<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;
use App\Models\Servicio;
use App\Models\Cliente_Externo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Agenda_Tecnico;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Cliente_Interno;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Historial_Servicios;
use App\Models\Tecnico;
use App\Services\StateCatalog;
use App\Services\StatusAssigner;

class ServicioMutations
{
    public static $entity_type = "service";
    //state
    const clientExternal= 2;
    const clientInternal= 1;
    // CREAR SERVICIO PARA CLIENTE INTERNO
    public function createInternal($root,array $args){
        $serviceData = $args['requestService'];
        $now=Carbon::now();
        $technicalId = Tecnico::find($serviceData['id_technician']);
        if(is_null($technicalId)){
            return [
                'message' => 'Tecnico no existe'
            ];
        }
        $clientId = Cliente_Interno::find($serviceData['id_client']);
        if(is_null($clientId)){
            return [
                'message' => 'Cliente no existe'
            ];
        }
        DB::beginTransaction();
        try{
            $service = Servicio::create([
                'requestsId' => $serviceData['id_requests'],
                'technicalId' => $serviceData['id_technician'],
                'clientId' => $serviceData['id_client'],
                'activityId' => $serviceData['id_activity'],
                'typeClient' => self::clientInternal,
                'titleService' => trim($serviceData['titleService']),
                'serviceDescription' => trim($serviceData['serviceDescription']),
                'serviceLocation' => trim($serviceData['serviceLocation']),
                'latitude' => $serviceData['latitude'],
                'longitude' => $serviceData['longitude'],
                'createdDateTime' => $now,
                'updatedDateTime' => $serviceData['updatedDateTime'],
                'status' => StateCatalog::STATUS_ACTIVE
            ]);
            $stateId = StatusAssigner::assignState($service, StatusAssigner::SERVICE_PENDING, self::$entity_type);
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
                'serviceDate' => $_service->createdDateTime,
                'createDate' => Carbon::now()
            ]);
            DB::commit();
            return[
                'message' => 'Servicio creado para cliente interno',
                'service' => $_service,
                'customer_internal' => $clientId
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
        $state = 4;
        $now=Carbon::now();

        $technicalId = Tecnico::find($serviceData['id_technician']);
        if(is_null($technicalId)){
            return [
                'message' => 'Tecnico no existe'
            ];
        }
        $clientId = Cliente_Externo::find($serviceData['id_client']);
        if(is_null($clientId)){
            return [
                'message' => 'Cliente no existe'
            ];
        }
        $associant = Asociacion_Cliente_Tecnico::where('clientId',$serviceData['id_client'])
        ->where('technicalId',$serviceData['id_technician'])->first();
        if(is_null($associant)){
            return [
                'message' => 'No existe relacion entre tecnico y cliente externo.'
            ];
        }
        DB::beginTransaction();
        try{
            $service = Servicio::create([
                'technicalId' => $serviceData['id_technician'],
                'clientId' => $serviceData['id_client'],
                'activityId' => $serviceData['id_activity'],
                'typeClient' => self::clientExternal,
                'titleService' => trim($serviceData['titleService']),
                'serviceDescription' => trim($serviceData['serviceDescription']),
                'latitude' => $serviceData['latitude'],
                'longitude' => $serviceData['longitude'],
                'createdDateTime' => $now,
                'updatedDateTime' => $serviceData['updatedDateTime'],
                'status' => StateCatalog::STATUS_ACTIVE
            ]);
            StatusAssigner::assignState($service,StatusAssigner::SERVICE_PENDING,self::$entity_type);
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

        $service = Servicio::find($serviceId);
        if(is_null($service->id)){
            return [
                'message' => 'Servicio no encontrado.'
            ];
        }

        DB::beginTransaction();
        try{
            $client = Cliente_Externo::find($service->clientId);
            $technician = Tecnico::find($service->technicianId);
            $service->finishDateTime_technician = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();

            StatusAssigner::assignState($service,StatusAssigner::SERVICE_COMPLETED,self::$entity_type);

            $service->save();
            $_service = Servicio::find($service->id);
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
        $serviceDateTime = Carbon::parse($serviceData['finishDateTime']);

        $service = Servicio::find($serviceId);
        if (is_null($service)) {
            return [
                'message' => 'Servicio no encontrado.',
            ];
        }

        $client = Cliente_Interno::find($clientId);
        if (is_null($client)) {
            return [
                'message' => 'Cliente no encontrado.',
            ];
        }

        $technician = Tecnico::find($technicianId);
        if (is_null($technician)) {
            return [
                'message' => 'Técnico no encontrado.',
            ];
        }

        DB::beginTransaction();
        try {
            $service->finishDateTime_client = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();
            $service->save();
            // Actualizar el estado a completado
            if($service->finishDateTime_client != null && $service->serviceDateTime_technician != null){
                StatusAssigner::assignState($service, StatusAssigner::SERVICE_COMPLETED, self::$entity_type);
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
        $serviceDateTime = Carbon::parse($serviceData['finishDateTime_technician']);

        $service = Servicio::find($serviceId);
        if (is_null($service)) {
            return [
                'message' => 'Servicio no encontrado.',
            ];
        }

        $client = Cliente_Interno::find($clientId);
        if (is_null($client)) {
            return [
                'message' => 'Cliente no encontrado.',
            ];
        }

        $technician = Tecnico::find($technicianId);
        if (is_null($technician)) {
            return [
                'message' => 'Técnico no encontrado.',
            ];
        }

        DB::beginTransaction();
        try {
            $service->finishDateTime_technician = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();
            $service->save();
            // Actualizar el estado a completado
            if($service->finishDateTime_client != null && $service->finishDateTime_technician != null){
                StatusAssigner::assignState($service, StatusAssigner::SERVICE_COMPLETED, self::$entity_type);
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
}
