<?php

namespace App\Services;

use App\Models\Tecnico;
use App\Models\Cliente_Interno;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Cliente_Externo;
use App\Models\Servicio;


class ValidationModels{
    public static function validationTechnician($objeto,$state){}
    public static function validationclientInternal($objeto,$state){}
    public static function validationclientExternal($objeto,$state){}
    public static function validationclientService($objeto,$state){}
}
