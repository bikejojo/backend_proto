<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\User;

class TecnicoMutations {
    public function create($root,array $args){
        //return $tecnico=Tecnico::create($args);
        $tecnicoData = $args['tecnicoRequest'];
        $tecnico = Tecnico::create([
            'nombre' => $tecnicoData['nombre'] ,
            'apellido'=>$tecnicoData['apellido'],
            'carnet_anverso'=>$tecnicoData['carnet_anverso'],
            'carnet_reverso'=>$tecnicoData['carnet_reverso'],
            'email'=>$tecnicoData['email'],
            'telefono'=>$tecnicoData['telefono'],
            'contrasenia'=>$tecnicoData['contrasenia'],
            'foto'=>$tecnicoData['foto'],
            'users_id'=>$tecnicoData['users_id'],
            'ciudades_id'=>$tecnicoData['ciudades_id'],
        ]);
        return $tecnico;
    }

    public function update($root,array $args){
        $id=Tecnico::find($args['id']);
        $tecnico = Tecnico::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'apellido'=>$args['apellido'],'carnet_anverso'=>$args['carnet_anverso'],
        'email'=>$args['email'],'contrasenia'=>$args['contrasenia'],
        'foto'=>$args['foto']]);
        return $tecnico->all();
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
