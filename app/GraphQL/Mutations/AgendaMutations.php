<?php

namespace App\GraphQL\Mutations;

use App\Models\Agenda_Tecnico;
use App\Models\Note;
use Carbon\Carbon;
use App\Models\Tipo_Actividad;
Use App\Models\Tipo_Estado;

class AgendaMutations
{

    public function index($root ,array $args){
        $id =$args['tecnico_id'];
        $today = now();
        $a = Agenda_Tecnico::where('tecnico_id', $id)->where('fecha_proxima','>=',$today)->orderBy('fecha_proxima','asc')->get();
        if ($a->isEmpty()) {
            throw new \GraphQL\Error\Error('No se encontró ninguna agenda para este técnico.');
        }

        return $a;
    }

    public function create($root , array $args){
        $estadoFin=5; // numero se requiere saber si poner en null fecha proxima !

        $agenda = new Agenda_Tecnico();
        $tecnico_id=$args['tecnico_id'];
        $cliente_d=$args['clientes_id'];
        $cita_id = $args['cita_id'];
        $fechaInicio = Carbon::now();

        if($args['estado_id']!==$estadoFin){
            $fechaProxima = $args['fecha_proxima'];
            $descripcion_proxima = $args['descripcio_proxima'];
            $agenda->fecha_hora_proxima  = $fechaProxima;
            $agenda->descripcion_proxima = $descripcion_proxima;
        }else{
            $agenda->fecha_hora_proxima = null;
            $agenda->descripcion_proxima = 'sin actividades';
        }
        if (isset($args['note_contenido']) && !empty($args['note_contenido'])) {
            $note = New Note();
            $note->descripcion=$args['note_contenido'];
            $note->save();
            $agenda->note_id = $note->id;
        }else{
            $agenda->note_id = null;
        }
        $agenda->fecha_hora_creada = $fechaInicio;
        #-----------------------------------------
        $fechaInicio = Carbon::parse($agenda->fecha_hora_registrada);
        $fechaProxima = Carbon::parse($agenda->fecha_hora_proxima);
        #-----------------------------------------}
        $agenda->duracion = $fechaInicio->diff($fechaProxima)->format('%h horas, %i minutos, %s segundos');
        #-----------------------------------------
        $agenda->tecnico_id = $tecnico_id;
        $agenda->cliente_id = $cliente_d;
        $agenda->estado_id = $args['estado_id'];
        $agenda->cita_id = $cita_id;
        $agenda->save();
        return $agenda;
    }
}
