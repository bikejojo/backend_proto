<?php

namespace App\Services;

use App\Models\Tecnico;
use App\Models\Solicitud;
use App\Models\Cliente_Externo;
use App\Models\Servicio;

class GetValidation{
    public static function validationTecnico($object){
        return($technician = Tecnico::find($object));
    }
}
