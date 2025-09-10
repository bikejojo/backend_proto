<?php

namespace App\GraphQL\Mutations;

use App\Models\Ciudad;

class CiudadMutations{
    // crea una ciudad nueva para el select de ciudades
   public function create($root,array $args){
      return $ciudad=Ciudad::create($args);
   }
   // actualizacion del nombre de la ciudad , en $args contiene los atributos que se mandan
   public function update($root,array $args){
      $id=Ciudad::find($args['id']);
      $ciudad=Ciudad::where('id',$id)->update(['name'=> $args['name']]);
      return $ciudad;
   }
   // eliminacion de la ciudad
   public function delete($root,array $args){
      $id = Ciudad::find($args['id']);
      if($id){
         ['message'=> 'Borrado no existoso'];
      }else{
         Ciudad::where('id',$id)->delete();
         ['message'=> 'Borrado existoso'];
      }
   }
}
