<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TecnicoMutations {
    public function create($root,array $args){
        //return $tecnico=Tecnico::create($args);
       /* $tecnicoData = $args['tecnicoRequest'];
        $tecnico = Tecnico::create([
            'nombre' => $tecnicoData['nombre'] ,
            'apellido'=>$tecnicoData['apellido'],
            'carnet_anverso'=>$tecnicoData['carnet_anverso'],
            'carnet_reverso'=>$tecnicoData['carnet_reverso'],
            'email'=>$tecnicoData['email'],
            'telefono'=>$tecnicoData['telefono'],
            'contrasenia'=>Hash::make($tecnicoData['contrasenia']),
            'foto'=>$tecnicoData['foto'],
            'users_id'=>$tecnicoData['users_id'],
            'ciudades_id'=>$tecnicoData['ciudades_id'],
        ]);
        return $tecnico;*/
        $tecnicoData = $args['tecnicoRequest'];
        $nombre = $tecnicoData['nombre'];
        $apellido = $tecnicoData['apellido'];
        $carnet_anverso=$tecnicoData['carnet_anverso'];
        $carnet_reverso=$tecnicoData['carnet_reverso'];
        $email=$tecnicoData['email'];
        $telefono=$tecnicoData['telefono'];
        $contrasenia=$tecnicoData['contrasenia'];
        $foto=$tecnicoData['foto'];
        $users_id=$tecnicoData['users_id'];
        $ciudades_id=$tecnicoData['ciudades_id'];

        if($nombre && $apellido && $carnet_anverso && $carnet_reverso &&
            $email && $telefono && $contrasenia&&$foto&&$users_id&&$ciudades_id ){
                $tecnico = Tecnico::create([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'carnet_anverso' => $carnet_anverso,
                    'carnet_reverso' => $carnet_reverso,
                    'email' => $email,
                    'telefono' => $telefono,
                    'contrasenia' => Hash::make($contrasenia),
                    'foto' => $foto,
                    'users_id' => $users_id,
                    'ciudades_id' => $ciudades_id,
                ]);
                return $tecnico;
        }
        return null;
    }

    public function update($root,array $args){
        /*$id=Tecnico::find($args['id']);
        $tecnico = Tecnico::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'apellido'=>$args['apellido'],'carnet_anverso'=>$args['carnet_anverso'],
        'email'=>$args['email'],'contrasenia'=>$args['contrasenia'],
        'foto'=>$args['foto']]);
        return $tecnico->all(); */
        $tecnicoData = $args['tecnicoRequest'];
        $tecnico = Tecnico::find($args['id']);

        if(!$tecnico){
            throw new \Exception('Tecnico no encontrado');
        }

        $tecnico->nombre = $tecnicoData['nombre']??$tecnico->nombre;
        $tecnico->apellido = $tecnicoData['apellido']??$tecnico->apellido;
        $tecnico->carnet_anverso = $tecnicoData['carnet_anverso'] ?? $tecnico->carnet_anverso;
        $tecnico->carnet_reverso = $tecnicoData['carnet_reverso']?? $tecnico->carnet_reverso;
        $tecnico->email = $tecnicoData['email']??$tecnico->email;
        $tecnico->telefono = $tecnicoData['telefono'] ?? $tecnico->telefono;
        $tecnico->contrasenia = isset($tecnicoData['contrasenia']) ? Hash::make($tecnicoData['contrasenia']): $tecnico->contrasenia;
        $tecnico->foto = $tecnicoData['foto'] ?? $tecnico->foto;
        $tecnico->users_id = $tecnicoData['users_id'] ?? $tecnico->users_id;
        $tecnico->ciudades_id = $tecnicoData['ciudades_id'] ?? $tecnico->ciudades_id;

        return $tecnico;
    }
    public function delete($root,array $args){
        $id=Tecnico::find($args['id']);
        if($id){
            return ['message' => 'Borrado No exitoso'];
        }else{
            $tecnico = Tecnico::where('id',$id)->delete();
            return ['message' => 'Borrado exitoso'];
        }
    }

}
