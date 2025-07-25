<?php

namespace App\Services;

use App\Models\Tecnico;
use App\Models\Cliente_Interno;
use App\Models\Asociacion_Cliente_Tecnico;
use App\Models\Cliente_Externo;
use App\Models\Publicidad;
use App\Models\Solicitud;
use App\Models\Servicio;
use App\Models\Agenda_Tecnico;
use App\Models\User;

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
        //dd(Cliente_Interno::where('id',$objeto)->first());
        if(!Cliente_Interno::find($objeto)){
            return[
                'message' => 'No existe Cliente Interno.'
            ];
        }else{
            return Cliente_Interno::find($objeto);
        }
    }
    public static function validationclientExternal($objeto){
        if(!Cliente_Externo::find($objeto)){
            return[
                'message' => 'No existe Cliente Externo.'
            ];
        }else{
            return Cliente_Externo::find($objeto);
        }
    }

    public static function validationExternalCLient($objeto){
        if(!Asociacion_Cliente_Tecnico::where('clientId',$objeto)->first()){
            return[
                'message' => 'No existe Cliente Externo.'
            ];
        }else{
            return Asociacion_Cliente_Tecnico::where('clientId',$objeto)->first();
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
    public static function validationService($objeto){
        if(!Servicio::find($objeto)){
            return[
                'message'=>'No existe el servicio.'
            ];
        }else{
            return Servicio::find($objeto);
        }
    }
    public static function validationServiceExternal($objeto){
        if(!Servicio::where('id',$objeto)->where('typeClient',2)->first()){
            return[
                'message'=>'No existe el servicio.'
            ];
        }else{
            return Servicio::find($objeto);
        }
    }
    public static function validation_Technician($objeto){
        return Tecnico::find($objeto);//where('userId',$objeto)->first();
    }//cambio realizdo e 25 de julio

    public static function validation_clientInternal($objeto){
        return Cliente_Interno::where('userId',$objeto)->first();
    }

    public static function validation_user($objeto){
        return User::find($objeto);
    }
}
