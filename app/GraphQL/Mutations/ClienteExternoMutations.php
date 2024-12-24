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

        // Inicializar para evitar error de variable no definida
        $cliente = null;

        // Verificar si el cliente ya existe para el mismo técnico
        $existe = Cliente_Externo::join('associationTechnClient', 'external_clients.id', '=', 'associationTechnClient.clientId')
            ->where('phoneNumber', $phone)
            ->where('associationTechnClient.technicalId', $tecnicoId)
            ->first();

        DB::beginTransaction();
        try {
            // Si ya existe y está activo, retornamos un mensaje
            if (isset($existe) && $existe->status === StateCatalog::STATUS_ACTIVE) {
                DB::rollBack();
                return [
                    'message' => 'El cliente ya está registrado y activo en la lista de este técnico.',
                    'customer_external' => $existe,
                    'technical' => $tecnico
                ];
            }

            // Verificar si el cliente existe globalmente
            $cliente = Cliente_Externo::where('phoneNumber', $phone)->first();

            // Si no existe, creamos uno nuevo
            if (!$cliente) {
                $cliente = Cliente_Externo::create([
                    'fullName' => $clienteData['fullName'],
                    'phoneNumber' => $clienteData['phoneNumber'],
                    'status' => StateCatalog::STATUS_ACTIVE
                ]);
                $cliente->save();
            }

            // Crear la asociación con el técnico
            Asociacion_Cliente_Tecnico::create([
                'clientId' => $cliente->id,
                'technicalId' => $tecnico->id,
                'dateTimeCreated' => Carbon::now(),
            ])->save();

            DB::commit();
            return [
                'message' => 'Creación de cliente exitosa.',
                'customer_external' => $cliente,
                'technical' => $tecnico
            ];
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


}
