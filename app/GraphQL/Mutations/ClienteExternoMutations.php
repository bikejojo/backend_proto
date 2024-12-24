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

        // Inicializar variable cliente
        $cliente = null;

        DB::beginTransaction();
        try {
            // 1. Buscar si el cliente existe globalmente (por teléfono)
            $cliente = Cliente_Externo::where('phoneNumber', $phone)->first();

            // 2. Si el cliente no existe, crearlo
            if (!$cliente) {
                $cliente = Cliente_Externo::create([
                    'fullName' => $clienteData['fullName'],
                    'phoneNumber' => $clienteData['phoneNumber'],
                    'status' => StateCatalog::STATUS_ACTIVE // Crear como activo por defecto
                ]);
                $cliente->save();
            }

            // 3. Verificar si ya existe una asociación con este técnico
            $asociacionExistente = Asociacion_Cliente_Tecnico::where('clientId', $cliente->id)
                ->where('technicalId', $tecnicoId)
                ->first();

            // 4. Si la asociación ya existe, retornar mensaje sin duplicar
            if ($asociacionExistente) {
                DB::rollBack();
                return [
                    'message' => 'El cliente ya está asociado con este técnico.',
                    'customer_external' => $cliente,
                    'technical' => $tecnico
                ];
            }

            // 5. Crear la asociación (sin importar el estado del cliente)
            $asociacion = Asociacion_Cliente_Tecnico::create([
                'clientId' => $cliente->id,
                'technicalId' => $tecnico->id,
                'dateTimeCreated' => Carbon::now(),
            ]);
            $asociacion->save();

            DB::commit();
            return [
                'message' => 'Cliente registrado y asociado con el técnico exitosamente.',
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
