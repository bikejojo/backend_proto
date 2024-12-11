<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico_Habilidad;
use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TecnicoHabilidadMutations{

    public function assign($root,array $args){
        $id = $args['technicianId'];

        $envHabilidades = $args['skills'];
        //valida que el array no venga vacio skills[]
        if (empty($envHabilidades)) {
            return [
                'message' => 'Debe registrar al menos una habilidad'
            ];
        }
    DB::beginTransaction();
    try{
        $habilidades=[];
        foreach($envHabilidades as $variable){
            //valida que el array tenga habilidad
            if (!isset($variable['id_skill']) || empty($variable['id_skill'])) {
                return [
                    'message' => 'No escogio la casilla de habilidad'
                ];
            }
            //valida que el array tegna experiencia
            if (!isset($variable['experience']) || empty($variable['experience'])) {
                return [
                    'message' => 'Debe registrar en cada habilidad una experiencia válida'
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
        $skill = Tecnico_Habilidad::where('technicianId',$id)
        ->leftjoin('skills','technician_skills.skillId','=','skills.id')->get();
        $technician = Tecnico::find($id);
        DB::commit();
        return [
            'message' => 'habilidades registradas.' ,
            'technician' => $technician,
            'skills' => $skill
        ];
       
    }catch(\Exception $e){
        DB::rollBack();
        return [
            'message' => 'Existe un error en.' . $e->getMessage()
        ];
    }
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
        DB::beginTransaction();
        try{
            // Eliminar las habilidades existentes del técnico
            Tecnico_Habilidad::where('technicianId', $tecnicoId)->delete();
            // Guardar las nuevas habilidades
            foreach ($habilidades as $habilidad) {
                Tecnico_Habilidad::create([
                    'technicianId' => $tecnicoId,
                    'skillId' => $habilidad['id_skill'],
                    'experience' => $habilidad['experience'],
                ]);
            }
            $habilidades = Tecnico_Habilidad::where('technicianId', $tecnicoId)
            ->leftjoin('skills','technician_skills.skillId','=','skills.id')->get();
            //return $habilidades;
            //dd($habilidades);
            DB::commit();
            return [
                'message' => 'habilidades actualizadas al tecnico OK' ,
                'technician' => $technician,
                'skills' => $habilidades
            ];
        } catch(\Exception $e){
            DB::rollback();
            return[
                'message' => 'Existe un error en.'. $e->getMessage()
            ];
        }
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
