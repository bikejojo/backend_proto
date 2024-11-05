<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;
use App\Models\Servicio;
use App\Models\Cliente_Externo;
use Carbon\Carbon;
use Nuwave\Lighthouse\Federation\Resolvers\Service;
use Illuminate\Support\Facades\DB;
use App\Models\Agenda_Tecnico;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Cliente_Interno;
use App\Models\Detalle_Agenda_Tecnico;
use App\Models\Tecnico;

class ServicioMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }
    // CREAR SERVICIO PARA CLIENTE INTERNO
    public function createInternal($root,array $args){
        $serviceData = $args['requestService'];
        $state = 4;
        $typeClient = 1;
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
        $service = Servicio::create([
            'requestsId' => $serviceData['id_requests'],
            'stateId' => $state,
            'technicalId' => $serviceData['id_technician'],
            'clientId' => $serviceData['id_client'],
            'activityId' => $serviceData['id_activity'],
            'typeClient' => $typeClient,
            'titleService' => trim($serviceData['titleService']),
            'serviceDescription' => trim($serviceData['serviceDescription']),
            'serviceLocation' => trim($serviceData['serviceLocation']),
            'createdDateTime' => $now,
            'updatedDateTime' => $serviceData['updatedDateTime'],
            'status' => 1
        ]);

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
            'serviceId' => $service->id,
            'typeClient' => $typeClient,
            'serviceDate' => $service->createdDateTime,
            'createDate' => Carbon::now()
        ]);
        return[
            'message' => 'Servicio creado para cliente interno',
            'service' => $service,
            'customer_internal' => $clientId
        ];
    }
    // CREAR SERVICIO PARA CLIENTE EXTERNO
    public function createExternal($root,array $args){
        $serviceData = $args['requestService'];
        $state = 4;
        $typeClient =2;
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
        //dd($associant);
        if(is_null($associant)){
            return [
                'message' => 'No existe relacion entre tecnico y cliente externo.'
            ];
        }
        DB::beginTransaction();
        try{
            $service = Servicio::create([
                'stateId' => $state,
                'technicalId' => $serviceData['id_technician'],
                'clientId' => $serviceData['id_client'],
                'activityId' => $serviceData['id_activity'],
                'typeClient' => $typeClient,
                'titleService' => trim($serviceData['titleService']),
                'serviceDescription' => trim($serviceData['serviceDescription']),
                'serviceLocation' => trim($serviceData['serviceLocation']),
                'createdDateTime' => $now,
                'updatedDateTime' => $serviceData['updatedDateTime'],
                'status' => 1
            ]);

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
                'serviceId' => $service->id,
                'typeClient' => $typeClient,
                'serviceDate' => $service->updatedDateTime,
                'createDate' => Carbon::now()
            ]);
            DB::commit();
            return[
                'message'=>'Servicio registrado para cliente externo',
                'service'=>$service,
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
        $finish = 5;
        DB::beginTransaction();
        try{
            $service = Servicio::find($serviceId);
            if(is_null($service->id)){
                return [
                    'message' => 'Servicio no encontrado.'
                ];
            }
            $client = Cliente_Externo::find($service->clientId);
            $technician = Tecnico::find($service->technicianId);
            $service->stateId = $finish;
            $service->finishDateTime = $serviceDateTime;
            $service->updatedDateTime = Carbon::now();
            $service->save();
            return [
                'message' => 'Servicio terminado',
                'customer_external' => $client ,
                'technician' => $technician ,
                'service' => $service
            ];
        }catch(\Exception $e){
            DB::rollBack();
            return [
                'message' => 'Error en la actualizacion de datos.' . $e->getMessage()
            ];
        }
    }

    public function delete($root , array $args){
        $serviceData = $args['requestService'];
        $serviceId=$serviceData['id_service'];
        $service = Servicio::find($serviceId);
        if($service != null){
            $service->status = 0;
            $service->save();
            return[
                'message' => 'El servicio se elimino.',
                'service' => $service
            ];
        }
    }
}
