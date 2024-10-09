<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Cita;
use App\Models\Agenda_Tecnico;

class CitaMutations
{

    public function create($root, array $args){
        $estadoId=7;
        #-----------
        $citaData = $args['citaRequest'];
        $cita = new Cita();
        $cita->fecha_hora_registrada = Carbon::now();
        $cita->fecha_hora_fin = Carbon::now();
        #-----------
        $cita->latitud = $citaData['latitud'];
        $cita->longitud = $citaData['longitud'];

        $descripcion_sol = trim($citaData['descripcion_cita']);

        $cita->descripcion_cita = $descripcion_sol;

        $descripcion_ubi = trim($citaData['descripcion_ubicacion']);

        $cita->descripcion_ubicacion = $descripcion_ubi;
        $cita->estado_id = $estadoId;
        $cita->solicitud_id = $citaData['solicitud_id'];
        $cita->resultado= '';
        $cita->save();

        return $cita;
    }
    public function update($root , array $args){
        $citaData = $args['citaRequest'];
        $cita_id = $citaData['id'];
        $cita = Cita::find($cita_id);
        switch($citaData['estado_id']){
            case 8:
                $cita->estado_id = $citaData['estado_id'];
                $cita->fecha_hora_fin = Carbon::now();
                $resultado_ = trim($citaData['resultado']);
                $cita->resultado= $resultado_;
                break;
            case 9:
                $cita->estado_id = $citaData['estado_id'];
                $cita->fecha_hora_fin = Carbon::now();
                $resultado_ = trim($citaData['resultado']);
                $cita->resultado= $resultado_;
                break;
            case 10:
                $cita->estado_id = $citaData['estado_id'];
                $cita->fecha_hora_fin = Carbon::now();
                $resultado_ = trim($citaData['resultado']);
                $cita->resultado= $resultado_;
                break;
        }
        #-------
        $cita->save();
        //return $cita;
        return [
            'cita' => $cita,
            'message' => 'cita actualizada'
        ];
    }

}
