<?php

namespace App\GraphQL\Mutations;


use App\Models\User;
use App\Models\Cliente_Externo;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Tecnico;
use App\Services\StateCatalog;
use App\Services\ValidationModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClienteExternoMutations{
    public function create($root, array $args) {
        $clienteData = $args['clientRequest'];
        $tecnicoId = $clienteData['technicalId'];
        $tecnico = ValidationModels::validationTechnician($tecnicoId);
        $phone = $clienteData['phoneNumber'];
        $external = Asociacion_Cliente_Tecnico::join('external_clients','associationTechnClient.clientId','=','external_clients.id')->join('technicians','associationTechnClient.technicalId','=','technicians.id')->where('external_clients.phoneNumber',$phone)->where('associationTechnClient.technicalId',$tecnico->id)->get();
        // Inicializar variable cliente
        $cliente = null;
        //dd(Asociacion_Cliente_Tecnico::join('external_clients','associationTechnClient.clientId','=','external_clients.id')->join('technicians','associationTechnClient.technicalId','=','technicians.id')->where('external_clients.phoneNumber',$phone)->where('associationTechnClient.technicalId',$tecnico->id)->get());
        DB::beginTransaction();
        try {
            if($external->isNotEmpty()) {
                //dd(1);
                DB::commit();
                return [
                    'message' => 'Cliente registrado con anterioridad en su agenda.',
                    'technical' => $tecnico
                ];
            }else{
                //ninguna asociacion entre el nuemro de telefono con la id del tecnico
                //dd(2);
                $cliente = Cliente_Externo::create([
                    'fullName' => $clienteData['fullName'],
                    'phoneNumber' => $clienteData['phoneNumber'],
                ]);
                $asociacion = Asociacion_Cliente_Tecnico::create([
                    'dateTimeCreated' => Carbon::now(),
                    'technicalId' => $tecnico->id,
                    'clientId' => $cliente->id,
                ]);
                DB::commit();
                return [
                    'message' => 'Cliente registrado y asociado con el técnico exitosamente.',
                    'customer_external' => $cliente,
                    'technical' => $tecnico
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error durante la creación: ' . $e->getMessage(),
                'customer_external' => null
            ];
        }
    }


    public function update($root ,array $args){
        $clientData = $args['clientRequest'];
        $client = Cliente_Externo::find($clientData['id_client']);
        $fullName = trim($clientData['fullName']);
        $phoneNumber = trim($clientData['phoneNumber']);
        $client->fullName= $fullName;
        $client->phoneNumber = $phoneNumber;
        $client->save();
        $technical=Asociacion_Cliente_Tecnico::where('clientId',$client->id)
        ->join('technicians','technicalId','=','technicians.id')
        ->select('technicians.*')
        ->first();
        return[
            'message' => 'Cliente actualizado exitoso!!' ,
            'customer_external' => $client,
            'technical' => $technical
        ];
    }
    public function delete($root ,array $args){
        $id=Cliente_Externo::find($args['id']);
        if(!$id){
            return ['message'=> 'Borrado no existoso'];
        }else{
            $id->status=StateCatalog::STATUS_LOW;
            $id->save();
            return ['message'=> 'Borrado existoso'];
        }
    }

    public function reactivate($root, array $args) {
        $clienteData = $args['clientRequest'];
        $tecnicoId = $clienteData['technicalId'];
        $phone = $clienteData['phoneNumber'];
        $tecnico = ValidationModels::validationTechnician($tecnicoId);

        // Buscar cliente inactivo para este técnico
        $cliente = Cliente_Externo::join('associationTechnClient', 'external_clients.id', '=', 'associationTechnClient.clientId')
            ->where('phoneNumber', $phone)
            ->where('associationTechnClient.technicalId', $tecnicoId)
            ->where('external_clients.status', StateCatalog::STATUS_LOW)
            ->first();

        if (!$cliente) {
            return [
                'message' => 'No se encontró un cliente inactivo con ese número para este técnico.',
                'customer_external' => null,
                'technical' => $tecnico
            ];
        }

        DB::beginTransaction();
        try {
            // Reactivar el cliente cambiando el estado
            $cliente->status = StateCatalog::STATUS_ACTIVE;
            $cliente->save();

            DB::commit();
            return [
                'message' => 'Cliente reactivado exitosamente.',
                'customer_external' => $cliente,
                'technical' => $tecnico
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error al reactivar el cliente: ' . $e->getMessage(),
                'customer_external' => null
            ];
        }
    }
}
