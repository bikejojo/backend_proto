<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;
use App\Models\Servicio;
use App\Models\Cita;
use App\Models\Cliente_Externo;
use Carbon\Carbon;
use Nuwave\Lighthouse\Federation\Resolvers\Service;

class ServicioMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }
    public function create($root,array $args){
        $client_e = 2;
        $process = 7;

        $serviceData=$args['requestService'];
        $service = new Servicio();
        $service->techinicalId=$serviceData['id_technician'];
        $service->clientId=$serviceData['id_client'];
        $service->typeClient=$client_e;
        $service->stateId=$process;
        $service->requestId=null;
        $service->programDate = $serviceData['programDate'];
        $service->serviceDescription=$serviceData['serviceDescription'];
        $service->requestDate = Carbon::now();
        $service->save();
        $clientId = $service->clientId;
        $cliente = Cliente_Externo::find($clientId);

        return [
            'message' => 'Servicio creado para cliente.',
            'customer_external' => $client_e,
            'service'=>$service
        ];
    }
    public function update($root,array $args){
        $clientOut=8;
        $ClientProgram=9;
        $complet=10;
        $serviceData=$args['requestService'];
        $serviceId = $serviceData['id_service'];
        $service=Servicio::find($serviceId);
        $stateIds=$serviceData['id_state'];
        //dd($stateIds);
        switch($stateIds){
            case 8:
                $service->stateId = $clientOut;
                $service->programDate = null;

                break;
            case 9:
                $service->stateId = $ClientProgram;
                $service->programDate = $serviceData['programDate'];
                break;
            case 10:
                $service->stateId = $complet;
                $service->programDate = Carbon::now();
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
}
