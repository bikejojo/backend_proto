<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;
use App\Models\Servicio;
use App\Models\Cita;
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
    }
    public function update($root,array $args){
        $clientOut=8;
        $ClientProgram=9;
        $complet=10;
        $serviceData=$args['requestService'];
        $serviceId = $serviceData['id_service'];
        $service=Servicio::find($serviceId);
        $stateIds=$service->stateId;
        switch($stateIds){
            case 8:
                $service->stateId = $clientOut;
                break;
            case 9:
                $service->stateId = $ClientProgram;
                break;
            case 10:
                $service->stateId = $complet;
                break;
            }
        $service->save();
        if($service->stateId == 10){
            $cita = new Cita();
            $cita->technicialId=$service->technicalId;
            $cita->clientId=$service->clientId;
            $cita->serviceId=$service->id;
            $cita->typeClient=$service->typeClient;
            $cita->citationDescription=$serviceData['citationDescription'];
            $cita->activityId=$serviceData['activityId'];
            $cita->cratedDate = Carbon::now();
            if($serviceData['nextDate']!= null){
                $cita->nextDate = $serviceData['nextDate'];
                $cita->save();

                
                return[
                    'message' => 'Servicio Completado',
                    'service' => $service ,
                    'messageN' => 'Creacion de cita.',
                    'cita' => $cita
                ];
            }
            $cita->save();

            return[
                'message' => 'Servicio Completado',
                'service' => $service ,
                'messageN' => 'Creacion de cita no necesaria.'
            ];
        }else{
            return [
                'message' => 'Servicio Actualizado',
                'service' => $service
            ];
        }
    }
}
