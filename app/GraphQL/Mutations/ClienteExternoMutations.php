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
            $phone = trim($clienteData['phoneNumber']);
            $name_full = trim($clienteData['fullName']);
            $external = Asociacion_Cliente_Tecnico::join('external_clients','associationTechnClient.clientId','=','external_clients.id')
            ->join('technicians','associationTechnClient.technicalId','=','technicians.id')
            ->where('external_clients.phoneNumber',$phone)
            ->where('associationTechnClient.technicalId',$tecnico->id)->first();

            DB::beginTransaction();
            try{
                if($external){
                    DB::commit();
                    return [
                        'message' => 'Cliente registrado con anterioridad en su agenda.',
                        'technical' => $tecnico
                    ];
                }
                $cliente = Cliente_Externo::join('associationTechnClient', 'external_clients.id', '=', 'associationTechnClient.clientId')
                ->where('associationTechnClient.full_name',$phone)
                ->where('associationTechnClient.phone_number',$name_full)
                ->where('associationTechnClient.technicalId', $tecnicoId)
                ->where('associationTechnClient.status', StateCatalog::STATUS_LOW)
                ->select('associationTechnClient.id','associationTechnClient.status','associationTechnClient.full_name','associationTechnClient.phone_number')
                ->first();

                Asociacion_Cliente_Tecnico::where('id', $cliente->id)
                ->update([
                    'status' => StateCatalog::STATUS_ACTIVE
                ]);

                DB::commit();
                return [
                    'message' => 'Cliente reactivado exitosamente.',
                    'customerExternal'=> [
                        'full_name' => $cliente->full_name,
                        'phone_number' => $cliente->phone_number
                    ],
                    'technical' => $tecnico
                ];

                $externo = Cliente_Externo::where('phoneNumber',$phone)->first();
                if($externo){
                    $asociacion  = Asociacion_Cliente_Tecnico::create([
                        'full_name'=> $externo->fullName,
                        'phone_number'=> $externo->phoneNumber,
                        'updated_by_technician'=> 0,
                        'version'=>1,
                        'clientId'=>$externo->id,
                        'technicalId'=>$tecnico->id,
                        'dateTimeCreated'=>Carbon::now(),
                        'status'=>1,
                    ]);
                    if($externo->fullName === $name_full){
                        DB::commit();
                        return [
                            'message' => 'Cliente registrado correctamente.!',
                            'technical' => $tecnico,
                            'customer_external'=> $externo
                        ];
                    }else{
                        $asociacion  = Asociacion_Cliente_Tecnico::create([
                            'full_name'=> $name_full,
                            'phone_number'=> $externo->phoneNumber,
                            'updated_by_technician'=> 0,
                            'version'=>1,
                            'clientId'=>$externo->id,
                            'technicalId'=>$tecnico->id,
                            'dateTimeCreated'=>Carbon::now(),
                            'status'=>1,
                        ]);
                        DB::commit();
                        return [
                            'message' => 'Se registro el contacto.!!',
                            'technical' => $tecnico,
                            'customer_external'=> $externo
                        ];
                    }
                }
                    // Crear cliente externo si no existe
                    $nuevoCliente = Cliente_Externo::create([
                        'fullName' => $name_full,
                        'phoneNumber' => $phone,
                        'status' => 1 ,
                    ]);

                    // Crear la asociación después de crear el cliente
                    $asociacion = Asociacion_Cliente_Tecnico::create([
                        'full_name' => $nuevoCliente->fullName,
                        'phone_number' => $nuevoCliente->phoneNumber,
                        'updated_by_technician' => 0,
                        'version' => 1,
                        'clientId' => $nuevoCliente->id,
                        'technicalId' => $tecnico->id,
                        'dateTimeCreated' => Carbon::now(),
                        'status' => 1,
                    ]);
                DB::commit();
                return [
                    'message' => 'Cliente registrado correctamente.',
                    'technical' => $tecnico,
                    'customer_external'=> $nuevoCliente
                ];

            } catch (\Exception $e) {
                DB::rollBack();
                return [
                    'message' => 'Se presento el siguiente error: ' . ' ' . $e->getMessage()
                ];
            }
    }


    public function update($root, array $args) {
        $clienteData = $args['clientRequest'];
        $tecnicoId = $clienteData['id_technician'];
        $clienteId = $clienteData['id_client'];
        $phone = trim($clienteData['phoneNumber']);
        $full_name = trim($clienteData['fullName']);

        // Validar que el técnico y el cliente existan
        $tecnico = ValidationModels::validationTechnician($tecnicoId);
        $cliente = ValidationModels::validationclientExternal($clienteId);

        // Buscar la asociación existente
        $asoc = Asociacion_Cliente_Tecnico::where('clientId', $clienteId)
            ->where('technicalId', $tecnico->id)
            ->first();

        DB::beginTransaction();
        try {
            // Si no existe la asociación, crearla
            if (!$asoc) {
                $asoc = Asociacion_Cliente_Tecnico::create([
                    'clientId' => $clienteId,
                    'technicalId' => $tecnico->id,
                    'full_name' => $full_name,
                    'phone_number' => $phone,
                    'version' => 1,
                    'updated_by_technician' => $tecnico->id,
                    'dateTimeCreated' => Carbon::now(),
                    'status' => 1
                ]);

                DB::commit();
                return [
                    'message' => 'Cliente asociado correctamente.',
                    'technical' => $tecnico,
                    'customerExternal' => [
                        'full_name' => $full_name,
                        'phone_number' => $phone
                    ]
                ];
            }

            // Verificar si el nombre o el teléfono han cambiado
            if ($full_name !== $asoc->full_name || $phone !== $asoc->phone_number) {
                $asoc->update([
                    'full_name' => $full_name,
                    'phone_number' => $phone,
                    'version' => $asoc->version + 1,
                    'updated_by_technician' => $tecnico->id
                ]);

                DB::commit();
                return [
                    'message' => 'Se realizó el cambio requerido.',
                    'technical' => $tecnico,
                    'customerExternal' => [
                        'full_name' => $full_name,
                        'phone_number' => $phone
                    ]
                ];
            }

            // Si no hay cambios, devolver mensaje sin actualizar
            DB::commit();
            return [
                'message' => 'No se produjo ningún cambio.',
                'technical' => $tecnico,
                'customerExternal' => [
                    'full_name' => $full_name,
                    'phone_number' => $phone
                ]
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'message' => 'El siguiente error se encuentra en: ' . $e->getMessage()
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

    public function reactivate($root, array $args) {
        $clienteData = $args['clientRequest'];
        $tecnicoId = $clienteData['technicalId'];
        $phone = $clienteData['phoneNumber'];
        $name_full = $clienteData['fullName'];
        $tecnico = ValidationModels::validationTechnician($tecnicoId);
        // Buscar cliente inactivo para este técnico
        $cliente = Cliente_Externo::join('associationTechnClient', 'external_clients.id', '=', 'associationTechnClient.clientId')
            ->where('associationTechnClient.full_name',$phone)
            ->where('associationTechnClient.phone_number',$name_full)
            ->where('associationTechnClient.technicalId', $tecnicoId)
            ->where('associationTechnClient.status', StateCatalog::STATUS_LOW)
            ->select('associationTechnClient.id','associationTechnClient.status','associationTechnClient.full_name','associationTechnClient.phone_number')
            ->first();

        if (!$cliente) {
            return [
                'message' => 'No se encontró un cliente inactivo con ese número de telefono para este técnico.',
                'customerExternal' => null,
                'technical' => $tecnico
            ];
        }

        DB::beginTransaction();
        try {
            Asociacion_Cliente_Tecnico::where('id', $cliente->id)
            ->update([
                'status' => StateCatalog::STATUS_ACTIVE
            ]);

            DB::commit();
            return [
                'message' => 'Cliente reactivado exitosamente.',
                'customerExternal'=> [
                    'full_name' => $cliente->full_name,
                    'phone_number' => $cliente->phone_number
                ],
                'technical' => $tecnico
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Error al reactivar el cliente: ' . $e->getMessage(),
                'customerExternal' => null
            ];
        }
    }
}
