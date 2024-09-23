<?php

namespace App\GraphQL\Mutations;

use App\Models\Agenda_Tecnico;
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
}
