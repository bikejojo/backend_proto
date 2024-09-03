<?php

namespace App\GraphQL\Mutations;



use App\Models\Cliente_Externo;

class ClienteExternoMutations{
    public function create($root ,array $args){
        return $cliente=Cliente_Externo::create($args);
    }
    public function update(){
        
    }
    public function delete(){
        
    }
}