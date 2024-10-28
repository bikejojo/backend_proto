<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;
use App\Models\Servicio;
use App\Models\Cita;
use App\Models\Cliente_Externo;
use Carbon\Carbon;
use Nuwave\Lighthouse\Federation\Resolvers\Service;
use App\Models\Agenda_Tecnico;
use App\Models\Detalle_Agenda_Tecnico;

class ServicioMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }
    // CREAR SERVICIO PARA CLIENTE EXTERNO
    public function create($root,array $args){
        $client_e = 2;
        $process = 7;
        $typeJob=1;
        $up = 1;


        $serviceData=$args['requestService'];
        $clientId = $serviceData['id_client'];
        $clientIds = Cliente_Externo::find($clientId);
        //dd($clientIds);
        if($clientIds != null){
            $service = new Servicio();
            $service->technicalId=$serviceData['id_technician'];
            $service->clientId=$serviceData['id_client'];
            $service->typeClient=$client_e;
            $service->stateId=$process;
            $service->status=$up;
            $service->requestsId=null;
            $service->programDate = $serviceData['programDate'];
            $service->serviceDescription=$serviceData['serviceDescription'];
            $service->requestsDate = Carbon::now();
            $service->save();
            $clientId = $service->clientId;
            $cliente = Cliente_Externo::find($clientId);
            $agenda=Agenda_Tecnico::where('technicianId',$service->technicalId)->first();
            $detalleAgenda=Detalle_Agenda_Tecnico::create([
                'clientId' => $service->clientId,
                'agendaTechnicalId' => $agenda->id,
                'citationId'=>null,
                'serviceId' =>$service->id ,
                'typeClient' => $service->typeClient,
                'createDate' => Carbon::now(),
                'typeJob' => $typeJob ,
                'serviceDate' => $service->programDate,
                'citationDate' => null,
            ]);
            return [
                'message' => 'Servicio creado para cliente.',
                'customer_external' => $cliente,
                'service'=>$service
            ];
        }else{
            return[
            'message' => 'Cliente Externo no existe.'];
        }
    }
    public function update($root,array $args){
        $typeJob=2;
        $clientOut=8;
        $ClientProgram=9;
        $complet=10;
        $serviceData=$args['requestService'];
        $serviceId = $serviceData['id_service'];
        $service=Servicio::find($serviceId);
        $stateIds=$serviceData['id_state'];

        switch($stateIds){
            case 8:
                $service->stateId = $clientOut;
                $service->programDate = null;
                $service->finishedDate = Carbon::now();
                break;
            case 9:
                $service->stateId = $ClientProgram;
                $service->programDate = $serviceData['programDate'];
                break;
            case 10:
                $service->stateId = $complet;
                $service->finishedDate = Carbon::now();
                break;
            }
        $service->save();
        if($service->stateId ===  $ClientProgram && $serviceData['programDate']!= null ){
            return [
                'message' => 'Servicio Actualizado',
                'service' => $service,
                'messageN' => 'Cliente reprogramo el servicio solicitado'
            ];
        }elseif($service->stateId ===  $clientOut){
            return [
                'message' => 'Servicio Actualizado',
                'service' => $service,
                'messageN' => 'Cliente esta ausente'
            ];
        }
        if($service->stateId == 10 && isset($serviceData['nextDate']) && !empty($serviceData['nextDate']) ){
            $cita = new Cita();
            $cita->technicialId=$service->technicalId;
            $cita->clientId=$service->clientId;
            $cita->serviceId=$service->id;
            $cita->typeClient=$service->typeClient;
            $cita->citationDescription=$serviceData['citationDescription'];
            $cita->activityId=$serviceData['id_activity'];
            $cita->cratedDate = Carbon::now();
            $cita->nextDate = $serviceData['nextDate'];
            $cita->save();
            $tecnicoId = $cita->technicialId;
            $agenda=Agenda_Tecnico::where('technicianId',$tecnicoId)->first();
            $detalleAgenda=Detalle_Agenda_Tecnico::create([
                'clientId' => $service->clientId,
                'agendaTechnicalId' => $agenda->id,
                'citationId'=>$cita->id,
                'serviceId' => null ,
                'typeClient' => $service->typeClient,
                'createDate' => Carbon::now(),
                'typeJob' => $typeJob ,
                'serviceDate' => null,
                'citationDate' => $cita->nextDate ,
            ]);
            return[
                'message' => 'Servicio Completado',
                'service' => $service ,
                'messageN' => 'Creacion de cita.',
                'cita' => $cita
            ];
        }
        return[
            'message' => 'Servicio Completado',
            'service' => $service ,
            'messageN' => 'Creacion de cita no necesaria.'
        ];
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
