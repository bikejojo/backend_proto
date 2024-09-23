<?php

namespace App\GraphQL\Mutations;

use App\Models\Agenda_Tecnico;
use app\Models\Detalle_Agenda_Tecnico;
use App\Models\Tipo_Actividad;
Use App\Models\Tipo_Estado;

class AgendaMutations
{

    public function index($root ,array $args){
        $tecnico_id = $args['tecnico_id'];
        // Obtener las agendas del técnico con sus detalles
        $tecnico=Agenda_Tecnico::find($tecnico_id);
        return $tecnico;
    }
    public function create(){
    }
    public function update(){
    }
}
