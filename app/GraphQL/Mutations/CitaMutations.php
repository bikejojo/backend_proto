<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\Cita;
use App\Models\Agenda_Tecnico;

class CitaMutations
{

    public function create($root, array $args){
        $estadoId=5;
        $diferencia='';

        $citaData = $args['citaRequest'];
        $cita = new Cita();
        $cita->fecha_hora_registrada = Carbon::now();
        $cita->fecha_hora_fin = Carbon::now();
        #-----------
        $fechaInicio = $cita->fecha_hora_registrada;
        $fechaFin = $cita->fecha_hora_fin;
        #-----------
        $cita->latitud = $citaData['latitud'];
        $cita->longitud = $citaData['longitud'];
        $cita->descripcion_solicitud = $citaData['descripcion_solicitud'];
        $cita->descripcion_ubicacion = $citaData['descripcion_ubicacion'];
        $cita->estado_id = $estadoId;
        $cita->solicitud_id = $citaData['solicitud_id'];
        $cita->resultado= '';
        $diferencia= ($fechaInicio->diff($fechaFin))->format('%h horas, %i minutos, %s segundos');
        $cita->duracion=$diferencia;
        $cita->save();

        return $cita;
    }
    public function update($root , array $args){
        $estadoId = 6;
        $citaData = $args['citaRequest'];
        $cita_id = $citaData['id'];
        $cita = Cita::find($cita_id);
        $cita->estado_id = $estadoId;
        $cita->fecha_hora_fin = Carbon::now();
        #-------
        if(isset($cita->fecha_hora_fin)){
            $fechaInicio = Carbon::parse($cita->fecha_hora_registrada);
            $fechaFin = Carbon::parse($cita->fecha_hora_fin);
            $cita->duracion = $fechaInicio->diff($fechaFin)->format('%h horas, %i minutos, %s segundos');
        }
        $cita->resultado= $citaData['resultado'];
        $cita->save();

        return $cita;
    }
    public function delete(){
    }
}
