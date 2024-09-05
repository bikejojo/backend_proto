<?php

namespace App\GraphQL\Mutations;

use App\Models\Preferencia_Habilidad;

class PreferenciaHabilidadMutations{
   public function create($root,array $args){
    return $ciudad=Preferencia_Habilidad::create($args);
   }      
   public function update($root,array $args){

   }    
   public function delete($root,array $args){}                                                                                                                                             
}