<?php

namespace App\GraphQL\Mutations;

use App\Models\Certificacion;

class CertificacionMutations{
    public function create($root,array $args){
        return Certificacion::create($args);
    }
    public function update($root,array $args){
        $id=Certificacion::find($args['id']);
        $certificacion=Certificacion::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'fecha_certificacion'=>$args['fecha_certificacion'],'foto_url'=>$args['foto_url']]);
    }
    public function delete($root,array $args){
        
    }

}