<?php

namespace App\GraphQL\Mutations;

use App\Models\Habilidad;

class HabilidadMutations {
   public function create($root,array $args){
    return $habilidad = Habilidad::create($args);
   }      
   public function update($root,array $args){
    $id= Habilidad::find($args['id']);
    $habilidad = Habilidad::where('id',$id)->update(['nombre'=>$args['nombre']]);
    return $habilidad;
   }    
   public function delete($root,array $args){}                                                                                                                                             
}