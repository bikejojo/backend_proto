<?php

namespace App\Services;

use App\Models\Tecnico;
use App\Models\Cliente_Interno;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Cliente_Externo;
use App\Models\Publicidad;
use App\Models\Solicitud;
use App\Models\Agenda_Tecnico;

class ValidationModels{
    public static function validationAgenda($objeto){
        if(!Agenda_Tecnico::where('technicianId',$objeto)->first()){
            return[
                'message' => 'No existe agenda para el tecnico.'
            ];
        }else{
            return Agenda_Tecnico::where('technicianId',$objeto)->first();
        }
    }

    public static function validationTechnician($objeto){

        if(!Tecnico::find($objeto)){
            return[
                'message' => 'No existe tecnico.'
            ];
        }else{
            return Tecnico::find($objeto);
        }
    }

    public static function validationclientInternal($objeto){
        if(!Cliente_Interno::find($objeto)){
            return[
                'message' => 'No existe Cliente Interno.'
            ];
        }else{
            return Cliente_Interno::find($objeto);
        }
    }
    public static function validationclientExternal($objeto){
        if(!Cliente_Externo::finc($objeto)){
            return[
                'message' => 'No existe Cliente Externo.'
            ];
        }else{
            return Cliente_Externo::find($objeto);
        }
    }
    public static function validationPublicity($objeto){
        if(!Publicidad::find($objeto)){
            return[
                'message'=>'No existe la publicidad'
            ];
        }else{
            return Publicidad::find($objeto);
        }
    }
    public static function validationRequest($objeto){
        if(!Solicitud::find($objeto)){
            return[
                'message'=>'No existe la solicitud'
            ];
        }else{
            return Solicitud::find($objeto);
        }
    }
}
