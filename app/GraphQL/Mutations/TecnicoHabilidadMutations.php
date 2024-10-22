<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico_Habilidad;
use App\Models\Tecnico;
use App\Models\User;

class TecnicoHabilidadMutations{

    public function assign($root,array $args){
        $id = $args['technicianId'];

        $envHabilidades = $args['skills'];
        //valida que el array no venga vacio skills[]
        if (!isset($envHabilidades) || empty($envHabilidades)) {
            return [
                'message' => 'Debe registrar al menos una habilidad'
            ];
        }

        $habilidades=[];
        foreach($envHabilidades as $variable){
            //valida que el array tegna experiencia
            if (!isset($variable['experience']) || empty($variable['experience'])) {
                return [
                    'message' => 'Debe registrar en cada habilidad una experiencia válida'
                ];
            }
            //valida que el array tenga habilidad
            if (!isset($variable['id_skill']) || empty($variable['id_skill'])) {
                return [
                    'message' => 'No escogio la casilla de habilidad'
                ];
            }
        }
        foreach ($envHabilidades as $recHabilidad) {

            $habilidadTecnico = Tecnico_Habilidad::create([
                'technicianId' => $id,
                'skillId' => $recHabilidad['id_skill'],
                'experience' => $recHabilidad['experience'],
                //'description' => $recHabilidad['description'],
            ]);
            $habilidades[] = $habilidadTecnico;
        }
        //dd($habilidades);
        $technician = Tecnico::find($id);
        return [
            'message' => 'habilidades registradas.' ,
            'technician' => $technician,
            'skills' => $habilidades
        ];
    }
    public function update($root,array $args){
        $tecnicoId = $args['id'];
        $habilidades = $args['skills'];
        $technician = Tecnico::find($tecnicoId);
        if(!$technician){
            return [
                'message' => 'No existe tecnico'
            ];
        }
        // Eliminar las habilidades existentes del técnico
        Tecnico_Habilidad::where('technicianId', $tecnicoId)->delete();
        // Guardar las nuevas habilidades
        foreach ($habilidades as $habilidad) {
            Tecnico_Habilidad::create([
                'technicianId' => $tecnicoId,
                'skillId' => $habilidad['id_skill'],
                'experience' => $habilidad['experience'],
                //'description' => $habilidad['description'],
            ]);
        }
        $habilidades = Tecnico_Habilidad::where('id_technician', $tecnicoId)->get();
        //return $habilidades;
        return [
            'message' => 'habilidades actualizadas al tecnico OK' ,
            'technician' => $technician,
            'skills' => $habilidades
        ];
    }
    public function userSkilsById($root , array $args){
        $userId = $args['id'];
        #dd($userId);
        $user = User::find($userId);
        #dd($user);
        $tecnico = Tecnico::where('userId',$user->id )->first();
        #dd($tecnico->id);
        $skills = Tecnico_Habilidad::where('technicianId', $tecnico->id)->get();
        //dd($skills);
        if($skills->isEmpty()){
            return [
                'message' => 'No tiene habilidades asignadas a este usuario',
                'skills' => null
            ];
        }

        return [
            'message' => 'Tiene habilidades asignadas a este usuario',
            'skills' => $skills
        ];
    }
}
