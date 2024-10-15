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

    $fecha = $args['agendaRequest']['fecha_creada'];
    $id = $args['agendaRequest']['tecnico_id'];
    $agenda = Agenda_Tecnico::where('tecnico_id',$id)
        ->where('fecha_creada',$fecha)
        ->get();
    if ($agenda->isEmpty()) {
        throw new \GraphQL\Error\Error('No se encontró ninguna agenda para este técnico.');
    }
     //return $agenda;
     return [
        'message' => 'listado de citas',
        'agenda_tecnico' => $agenda,
     ];
    }

    public function indexTecnico($root ,array $args){
        $id = $args['agendaRequest']['tecnico_id'];
    /*    $agenda = Agenda_Tecnico::where('tecnico_id',$id)
            ->get();*/
        $agenda = Agenda_Tecnico::with('tecnicos', //,'citas' ,'citas' , 'citas.solicitudes.solicituds','actividads'
        'clientes','actividads','citas','citas.solicitudes','citas.solicitudes.solicituds')->where('tecnico_id',$id)->get();
        if ($agenda->isEmpty()) {
            throw new \GraphQL\Error\Error('No se encontró ninguna agenda para este técnico.');
        }
         //return $agenda;
         return [
            'message' => 'listado de citas',
            'agenda_tecnico' => $agenda,
         ];
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
            $descripcion_ = trim($args['descripcio_proxima']);
            $descripcion_proxima = $descripcion_;
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
