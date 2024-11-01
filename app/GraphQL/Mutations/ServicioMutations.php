<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;
use App\Models\Servicio;
use App\Models\Cita;
use App\Models\Cliente_Externo;
use Carbon\Carbon;
use Nuwave\Lighthouse\Federation\Resolvers\Service;
use App\Models\Agenda_Tecnico;
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
        $technicalId = Tecnico::find($serviceData['id_technician']);
        if(is_null($technicalId)){
            return [
                'message' => 'Tecnico no existe'
            ];
        }
        $clientId = Cliente_Interno::find($serviceData['id_client']);
        if(is_null($clientId)){
            return [
                'message' => 'Tecnico no existe'
            ];
        }
        $service = Servicio::create([
            'stateId' => $state,
            'requestsId' => $serviceData['id_requests'],
            'technicalId' => $serviceData['id_technician'],
            'clientId' => $serviceData['id_client'],
            'typeClient' => $typeClient,
            'serviceDescription' => $serviceData['serviceDescription'],
            'createdDateTime' => Carbon::now(),
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
        $service = Servicio::create([
            'stateId' => $state,
            'technicalId' => $serviceData['id_technician'],
            'clientId' => $serviceData['id_client'],
            'typeClient' => $typeClient,
            'serviceDescription' => $serviceData['serviceDescription'],
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
        return[
            'message'=>'Servicio registrado para cliente externo',
            'service'=>$service,
            'customer_external' => $clientId
        ];
    }
    public function update($root,array $args){

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
