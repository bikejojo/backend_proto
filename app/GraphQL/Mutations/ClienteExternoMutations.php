<?php

namespace App\GraphQL\Mutations;


use App\Models\User;
use App\Models\Cliente_Externo;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Tecnico;
use Carbon\Carbon;

class ClienteExternoMutations{
    public function create($root, array $args){
    //dd($args['clientRequest']);
        $clienteData = $args['clientRequest'];
        // Crear el cliente en la base de datos

        $cliente = Cliente_Externo::create([
            'fullName' => $clienteData['fullName'],
            'phoneNumber' => $clienteData['phoneNumber']
        ]);
        //dd($clientId);
        $cliente->save();

        $asociacion = Asociacion_Cliente_Tecnico::create([
            'clientId' => $cliente->id,
            'technicalId' => $clienteData['technicalId'],
            'dateTimeCreated' => Carbon::now(),
        ]);

        $asociacion->save();
        $tecnico = Tecnico::find($clienteData['technicalId']);

        return [
            'message' => 'Creacion Cliente exitoso!',
            'clients' => $cliente,
            'technical' => $tecnico
        ];
    }
    public function update($root ,array $args){
        $clientData = $args['clientRequest'];
        $client = Cliente_Externo::find($args['id']);
        $clientId = $client->id;
        $user = User::find($client['userId']);
        /*$cliente = Cliente_Externo::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'email'=>$args['email'],'metodo_login'=>$args['metodo_login'],'foto'=>$args['foto'],'users_id'=>$args['users_id']]);*/
        //return Cliente_Externo::find($id);
        if ($client==null){
            throw new \Exception('Client not found.');
        }
        $firstName = ($client['fullName']);
        $email = ($client['email']);
        $client->loginMethod=$clientData['loginMethod'];
        $client->userId = $clientData['userId'];
        $client->cityId = $clientData['cityId'];

        $client->save();
        $user->email = $email ?? $user->email;
        $user->save();

        return[
            'message' => 'Cliente actualizado exitoso!!' ,
            'client' => $client
        ];
    }
    public function delete($root ,array $args){
        $id=Cliente_Externo::find($args['id']);
        //dd($id);
        if(!$id){
            return ['message'=> 'Borrado no existoso'];
        }else{
            $id->delete();
            return ['message'=> 'Borrado existoso'];
        }
    }


}
