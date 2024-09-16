<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico_Habilidad;
use App\Models\Tecnico;

class TecnicoHabilidadMutations{

    public function assign($root,array $args){
        #return $tecnico=Tecnico_Habilidad::create($args);
        $id = $args['tecnico_id'];
        $envHabilidades = $args['habilidades'];
        $habilidades=[];
        foreach ($envHabilidades as $recHabilidad) {
            $habilidadTecnico = Tecnico_Habilidad::create([
                'tecnico_id' => $id,
                'habilidad_id' => $recHabilidad['habilidad_id'],
                'experiencia' => $recHabilidad['experiencia'],
                'descripcion' => $recHabilidad['descripcion'],
            ]);
        }
        $habilidades = $habilidadTecnico;
        return $habilidades;
    }
    public function update($root,array $args){
       $tecnico = Tecnico::find($args['tecnico_id']);
       foreach($args['habilidades'] as $habilidad){
            $existe = Tecnico_Habilidad::where('tecnico_id',$args['tecnico_id'] &&
            'habilidad_id',$habilidad['habilidad_id'])
            ->first();

        if($existe){
            $existe->update([
                'experiencia' => $habilidad['experiencia'],
                'descripcion' => $habilidad['descripcion'],
            ]);
        }else{
            Tecnico_Habilidad::create([
                'tecnico_id' => $args['tecnico_id'],
                'habilidad_id' => $habilidad['habilidad_id'],
                'experiencia'=>$habilidad['experiencia'],
                'descripcion'=>$habilidad['descripcion'],
            ]);
        }
       }
       return $tecnico->load('habilidades');
    }
    public function delete($root,array $args){

    }

}
