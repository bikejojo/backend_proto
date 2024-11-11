<?php

namespace App\GraphQL\Mutations;

use App\Models\Cliente_Interno;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Tecnico;
use App\Services\StatusAssigner;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SolicitudesMutations
{
    public static  $entity_type = 'request';
    const status_cancel = 0;
    const status_accept = 1;

    public function create($root , array $args){
        $requestData = $args['requestRequest'];
        $description = trim($requestData['requestDescription']);
        $technicianId=$requestData['id_technician'];
        $clientId =$requestData['id_client'];
        $now=Carbon::now();

        $client=Cliente_Interno::find($clientId);
        if(!$client){
            return [ 'message' => 'Cliente no encontrado.'];
        }

        $technician=Tecnico::find($technicianId);
        if(!$technician){
            return [ 'message' => 'Tecnico no encontrado.'];
        }
        DB::beginTransaction();
            try{
            ##########################
            $request = new Solicitud();
            $request->clientId = $client->id;
            $request->technicianId = $technician->id;
            $request->requestDescription = $description;
            $request->status = self::status_accept;
            $request->registrationDateTime = $now;
            $request->save();
            ##########################
            $stateAssign = StatusAssigner::assignState($request,StatusAssigner::REQUEST_PENDING, self::$entity_type);
            $_request = Solicitud::find($request->id);
            DB::commit();
            return [
                'message' => 'Solicitud registrada',
                'requests' => $_request,
                'client' => $client,
                'technician' => $technician
            ];
            DB::rollBack();
        }catch (\Exception $e){return ['message' => 'El error es.'. $e->getMessage()];}
    }

    public function cancelRequestTechnician($root,array $args){
        // tipo 3
        //$requestData = $args['requestRequest'];
        $requestId = $args['id'];
        /*$requestId = $args['id_request'];
        $technicianId = $args['id_technician'];*/
        $request = Solicitud::find($requestId);
        ###################################3
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        //$tecnico = Tecnico::find($technicianId);
        $cliente = Cliente_Interno::find($clientId);
        /*if($tecnico){
            return ['message'=> 'No existe tecnico']
        }*/
        $tecnico = Tecnico::find($tecnicoId);
        $stateAssign = StatusAssigner::assignState($request,StatusAssigner::REQUEST_REJECTED, self::$entity_type);
        //$stateAssign = StatusAssigner::assignState($tecnico,$request,StatusAssigner::REQUEST_REJECTED, self::$entity_type);
        //$request->status= self::status_cancel;
        $_request = Solicitud::find($request->id);
        $request->save();
        return[
            'message'=>'Solicitud rechazada por el tecnico',
            'requests'=>$_request,
            'client' => $cliente,
            'technician' => $tecnico
        ];
    }

    public function cancelRequestClient($root,array $args){
        // tipo 2
        //$requestData = $args['requestRequest'];
        $requestId = $args['id'];
        $request = Solicitud::find($requestId);
        ###################################3
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        $cliente = Cliente_Interno::find($clientId);
        $tecnico = Tecnico::find($tecnicoId);
        $stateAssign = StatusAssigner::assignState($request,StatusAssigner::REQUEST_REJECTED, self::$entity_type);
        //$request->status= self::status_cancel;
        $_request = Solicitud::find($request->id);
        $request->save();
        return[
            'message'=>'Solicitud rechazada por el tecnico',
            'requests'=>$_request,
            'client' => $cliente,
            'technician' => $tecnico
        ];
    }

    public function acceptRequest($root,array $args){
        // tipo 2
        $requestId = $args['id'];
        $request = Solicitud::find($requestId);
        $clientId = $request->clientId;
        $tecnicoId=$request->technicianId;
        $cliente = Cliente_Interno::find($clientId);
        $tecnico = Tecnico::find($tecnicoId);
        $stateAssign = StatusAssigner::assignState($request,StatusAssigner::REQUEST_ACCEPTED, self::$entity_type);
        //$request->status= self::status_accept;
        $request->save();
        $_request = Solicitud::find($request->id);
        return[
            'message'=>'solicitud confirmada',
            'requests'=>$_request,
            'messageService' => 'Se agendara el servico en un momento',
            'client' => $cliente,
            'technician' => $tecnico
        ];
    }


}
