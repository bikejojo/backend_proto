<?php

namespace App\GraphQL\Mutations;

use App\Models\Agenda_Tecnico;
use App\Models\Note;
use Carbon\Carbon;
use App\Models\Tipo_Actividad;
Use App\Models\Tipo_Estado;

class AgendaMutations
{

    public function indexTecnico($root ,array $args){
        $agendaData = $args['agendaRequest'];
    }

}
