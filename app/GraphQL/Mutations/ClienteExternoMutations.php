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
    public function create($root, array $args){
        $clienteData = $args['clientRequest'];
        $tecnicoId = $clienteData['technicalId'];
        // Crear el cliente en la base de datos
        $tecnico = ValidationModels::validationTechnician($tecnicoId);//Tecnico::find($clienteData['technicalId']);
        $phone=$clienteData['phoneNumber'];
        $existe = Cliente_Externo::join('associationTechnClient','external_clients.id','=','associationTechnClient.clientId')
        ->where('phoneNumber',$phone)->where('associationTechnClient.technicalId',$tecnicoId)->first();
        DB::beginTransaction();
        try{
            if($existe){
                if($existe->status === StateCatalog::STATUS_ACTIVE ){
                    return [
                        'message' => 'cliente se registro con anterioridad en la lista!'
                    ];
                }else{
                    $existe->status = StateCatalog::STATUS_ACTIVE;
                    $existe->save();
                    return [
                        'message' => 'cliente se volvio a habilitar!'
                    ];
                }
            }else{
                $cliente = Cliente_Externo::create([
                    'fullName' => $clienteData['fullName'],
                    'phoneNumber' => $clienteData['phoneNumber'],
                    'status' => StateCatalog::STATUS_ACTIVE
                ]);
                $cliente->save();
                $asociacion = Asociacion_Cliente_Tecnico::create([
                    'clientId' => $cliente->id,
                    'technicalId' => $tecnico->id,
                    'dateTimeCreated' => Carbon::now(),
                ]);
                    $asociacion->save();
            }
            DB::commit();
            return [
                'message' => 'Creacion Cliente exitoso!',
                'customer_external' => $cliente,
                'technical' => $tecnico
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'El siguiente error es esto '.$e->getMessage(),
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


}
