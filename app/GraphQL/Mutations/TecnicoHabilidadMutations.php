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
        $tecnicoId = $args['id'];
        $habilidades = $args['habilidades'];

        // Buscar el técnico primero
        $tecnico = Tecnico::findOrFail($tecnicoId);

        // Eliminar las habilidades existentes del técnico
        Tecnico_Habilidad::where('tecnico_id', $tecnicoId)->delete();

        // Guardar las nuevas habilidades
        foreach ($habilidades as $habilidad) {
            Tecnico_Habilidad::create([
                'tecnico_id' => $tecnicoId,
                'habilidad_id' => $habilidad['habilidad_id'],
                'experiencia' => $habilidad['experiencia'],
                'descripcion' => $habilidad['descripcion'],
            ]);
        }

        // Retornar las nuevas habilidades
        return Tecnico_Habilidad::where('tecnico_id', $tecnicoId)->get();
    }

}
