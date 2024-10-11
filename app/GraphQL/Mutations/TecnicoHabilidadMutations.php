<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico_Habilidad;
use App\Models\Tecnico;

class TecnicoHabilidadMutations{

    public function assign($root,array $args){
        #return $tecnico=Tecnico_Habilidad::create($args);
        $id = $args['technicianId'];
        $envHabilidades = $args['skills'];
        $habilidades=[];
        foreach ($envHabilidades as $recHabilidad) {
            $habilidadTecnico = Tecnico_Habilidad::create([
                'technicianId' => $id,
                'skillId' => $recHabilidad['skillId'],
                'experience' => $recHabilidad['experience'],
                'description' => $recHabilidad['description'],
            ]);

            $habilidades[] = $habilidadTecnico;
        }
        return $habilidades;
        /*return [
            'message' => 'habilidades asignadas al tecnico OK'
            'assings' => $habilidades
        ];*/
    }
    public function update($root,array $args){
        $tecnicoId = $args['id'];
        $habilidades = $args['skills'];

        // Eliminar las habilidades existentes del técnico
        Tecnico_Habilidad::where('technicianId', $tecnicoId)->delete();

        // Guardar las nuevas habilidades
        foreach ($habilidades as $habilidad) {
            Tecnico_Habilidad::create([
                'technicianId' => $tecnicoId,
                'skillId' => $habilidad['skillId'],
                'experience' => $habilidad['experience'],
                'description' => $habilidad['description'],
            ]);
        }
        $habilidades = Tecnico_Habilidad::where('technicianId', $tecnicoId)->get();
        // Retornar las nuevas habilidades
        return $habilidades;
    }

}
