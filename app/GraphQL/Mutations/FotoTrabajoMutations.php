<?php

namespace App\GraphQL\Mutations;

use App\Models\Foto_Trabajo;

class FotoTrabajoMutations{
    public function create($root,array $args){
        return $foto = Foto_Trabajo::create($args);
    }
    public function update($root,array $args){
        $id=Foto_Trabajo::find($args['id']);
        $foto_trabajo = Foto_Trabajo::where('id',$id)->update(['foto'=>$args['foto'],'irl_foto'=>$args['url_foto']]);
    }
    public function delete($root,array $args){
        $id=Foto_Trabajo::find($args['id']);
        if($id){
            return ['message' => 'borrado no exitoso'];
        }else{
            Foto_Trabajo::where('id',$id)->delete();
            return ['message' => 'borrado exitoso'];
        }
    }

}