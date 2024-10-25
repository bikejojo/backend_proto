<?php

namespace App\GraphQL\Mutations;


use App\Models\User;
use App\Models\Cliente_Externo;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Tecnico;
use Carbon\Carbon;

class ClienteExternoMutations{
    public function create($root, array $args){
        $clienteData = $args['clientRequest'];
        // Crear el cliente en la base de datos
        $tecnico = Tecnico::find($clienteData['technicalId']);

        if($tecnico == null){
            return [
                'message'=>'Usuario tecnico no encontrado'
            ];
        }
        $phone=$clienteData['phoneNumber'];
        $existe = Cliente_Externo::where('phoneNumber',$phone)->first();
        if($existe){
            return [
                'message' => 'cliente se registro con anterioridad en la lista!'
            ];
        }else{
            $cliente = Cliente_Externo::create([
                'fullName' => $clienteData['fullName'],
                'phoneNumber' => $clienteData['phoneNumber']
            ]);
            //dd($clientId);
            $cliente->save();
        }
        $asociacion = Asociacion_Cliente_Tecnico::create([
            'clientId' => $cliente->id,
            'technicalId' => $tecnico->id,
            'dateTimeCreated' => Carbon::now(),
        ]);

        $asociacion->save();

        return [
            'message' => 'Creacion Cliente exitoso!',
            'customer_external' => $cliente,
            'technical' => $tecnico
        ];
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
            $id->delete();
            return ['message'=> 'Borrado existoso'];
        }
    }


}
