<?php

namespace App\GraphQL\Mutations;


use App\Models\Cliente_Externo;
use App\Models\Asociacion_Cliente_Tecnico;
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
        $external = Asociacion_Cliente_Tecnico::join('external_clients','associationTechnClient.clientId','=','external_clients.id')
        ->join('technicians','associationTechnClient.technicalId','=','technicians.id')
        ->where('external_clients.phoneNumber',$phone)
        ->where('associationTechnClient.technicalId',$tecnico->id)->get();
        // Inicializar variable cliente
        $cliente = null;
        DB::beginTransaction();
        try {
            if($external->isNotEmpty()) {

                DB::commit();
                return [
                    'message' => 'Cliente registrado con anterioridad en su agenda.',
                    'technical' => $tecnico
                ];
            }else{

                $cliente = Cliente_Externo::where('phoneNumber',$phone)->first();
                $asociacion = Asociacion_Cliente_Tecnico::create([
                    'dateTimeCreated' => Carbon::now()->copy(),
                    'technicalId' => $tecnico->id,
                    'clientId' => $cliente->id,
                    'status' => StateCatalog::STATUS_ACTIVE
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


    public function update($root, array $args) {
        $clientData = $args['clientRequest'];
        $client = Cliente_Externo::find($clientData['id_client']);
        $technical = ValidationModels::validationTechnician($clientData['id_technician']);

        $fullName = trim($clientData['fullName']);
        $phoneNumber = trim($clientData['phoneNumber']);

        DB::beginTransaction();
        try {
            // Verificar si los datos del cliente realmente cambiaron
            if ($client->fullName === $fullName && $client->phoneNumber === $phoneNumber) {
                // Los datos no han cambiado, no es necesario crear un nuevo cliente
                return [
                    'message' => 'No se realizaron cambios. El cliente ya tiene los mismos datos.',
                    'customer_external' => $client,
                    'technical' => $technical
                ];
            }

            // Crear un nuevo cliente solo si los datos son diferentes
            $newClient = Cliente_Externo::create([
                'fullName' => $fullName,
                'phoneNumber' => $phoneNumber,
                'status' => StateCatalog::STATUS_ACTIVE
            ]);

            // Desactivar la asociación anterior (opcional)
            Asociacion_Cliente_Tecnico::where('clientId', $client->id)
                ->where('technicalId', $technical->id)
                ->update(['status' => StateCatalog::STATUS_LOW]);

            // Crear nueva asociación entre el técnico y el nuevo cliente
            Asociacion_Cliente_Tecnico::create([
                'clientId' => $newClient->id,
                'technicalId' => $technical->id,
                'status' => StateCatalog::STATUS_ACTIVE,
                'dateTimeCreated' => Carbon::now()
            ]);

            DB::commit();
            return [
                'message' => 'Cliente actualizado exitosamente.',
                'customer_external' => $newClient,
                'technical' => $technical
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error durante la actualización: ' . $e->getMessage()
            ];
        }
    }

    public function delete($root ,array $args){
        DB::beginTransaction();
        try {
            $idCliente=Cliente_Externo::find($args['id_client']);
            $idTecnico=ValidationModels::validationTechnician($args['id_technician']);
            $idAsociasion = Asociacion_Cliente_Tecnico::where('clientId',$idCliente->id)->where('technicalId',$idTecnico->id)->first();
            if(!$idAsociasion){
                return ['message'=> 'Borrado no existoso'];
            }else{
                $idAsociasion->status=StateCatalog::STATUS_LOW;
                $idAsociasion->save();
                DB::commit();
                return [
                    'message'=> 'Borrado existoso'
                ];
            }
        }catch (\Exception $e){
            DB::rollBack();
            return[
                'message' => 'El problema es el siguiente.  ' . $e->getMessage()
            ];
        }
    }

    /*public function reactivate($root, array $args) {
        $clienteData = $args['clientRequest'];
        $tecnicoId = $clienteData['technicalId'];
        $clientId  = $clienteData['clientId'];
        $phone = $clienteData['phoneNumber'];
        $cliente = ValidationModels::validationclientExternal($clientId);
        $tecnico = ValidationModels::validationTechnician($tecnicoId);

        // Buscar cliente inactivo para este técnico
        $cliente = Cliente_Externo::join('associationTechnClient', 'external_clients.id', '=', 'associationTechnClient.clientId')
            ->where('phoneNumber', $phone)
            ->where('id',$clientId)
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
    }*/
}
