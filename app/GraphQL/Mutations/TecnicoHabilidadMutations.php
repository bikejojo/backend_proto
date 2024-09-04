<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico_Habilidad;

class TecnicoHabilidadMutations{
    public function create($root,array $args){
        return $tecnico=Tecnico::create($args);
    }
    public function update($root,array $args){
        $id=Tecnico::find($args['id']);
        $tecnico = tecnico::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'apellido'=>$args['apellido'],'carnet-anverso'=>$args['carnet-anverso'],
        'carnet-reverso'=>$agrs['carnet-reverso'],'correo'=>$agrs['correo'],'contrasenia'=>$args['contrasenia'],
        'foto'=>$agrs['foto']]);
        return $tecnico->all();
    }
    public function delete($root,array $args){
        
    }

}