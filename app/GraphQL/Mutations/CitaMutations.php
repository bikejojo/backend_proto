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
        $cita->fecha_registrada = Carbon::now();
        $cita->fecha_fin = Carbon::now();
        #-----------
        $fechaInicio = $cita->fecha_registrada;
        $fechaFin = $cita->fecha_fin;
        #-----------
        $cita->latitud = $citaData['latitud'];
        $cita->longitud = $citaData['longitud'];
        $cita->descripcion_solicitud = $citaData['descripcion_solicitud'];
        $cita->descripcion_ubicacion = $citaData['descripcion_ubicacion'];
        $cita->solicitud_id = $estadoId;
        $cita->resultado= '';
        $diferencia= ($fechaInicio->diff($fechaFin))->format('%h horas, %i minutos, %s segundos');
        $cita->duracion=$diferencia;
        $cita->save();
    }
    public function update($root , array $args){
        $estadoId = 6;
        $citaData = $args['citaRequest'];
        $cita_id = $citaData['id'];
        $cita = Cita::find($cita_id);
        $cita->estado_id = $estadoId;
        $cita->fecha_fin = Carbon::now();
        #-------
        $fechaInicio = $cita->fecha_registrada;
        $fechaFin = $cita->fecha_fin;
        #-------
        $diferencia= ($fechaInicio->diff($fechaFin))->format('%h horas, %i minutos, %s segundos');
        $cita->duracion=$diferencia;
        $cita->resultado= $citaData['resultado'];
        $cita->save();
    }
    public function delete(){
    }
}
