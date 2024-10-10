<?php

namespace App\GraphQL\Mutations;

use App\Models\Ciudad;

class CiudadMutations{
   public function create($root,array $args){
      return $ciudad=Ciudad::create($args);
   }
   public function update($root,array $args){
      $id=Ciudad::find($args['id']);
      $ciudad=Ciudad::where('id',$id)->update(['name'=> $args['name']]);
      return $ciudad;
   }
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
