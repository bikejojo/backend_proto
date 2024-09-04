<?php

namespace App\GraphQL\Mutations;



use App\Models\Cliente_Externo;

class ClienteExternoMutations{
    public function create($root ,array $args){
        return $cliente=Cliente_Externo::create($args);
    }
    public function update($root ,array $args){
        $id = Cliente_Externo::find($args['id']);
        $cliente = Cliente_Externo::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'email'=>$args['email'],'metodo_login'=>$args['metodo_login'],'foto'=>$args['foto'],'users_id'=>$args['users_id']]);
        return Cliente_Externo::find($id);
    }
    public function delete($root ,array $args){
        
    }
}