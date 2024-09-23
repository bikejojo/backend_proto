<?php

namespace App\GraphQL\Mutations;

use App\Models\Solicitud;
use App\Models\Foto_Solicitud;
use App\Models\Solicitud_Detalle;

use Carbon\Carbon;

class SolicitudesMutaions
{
    public function create($root , array $args){
        $tecnico_id = $args['tecnico_id'];
        $cliente_id = $args['cliente_id'];
        $fecha_programada = $args['fecha_tiempo_registrado'];
        $fecha_carbon = Carbon::today();
        if($fecha_carbon->isSameDay($fecha_programada)){
            $fecha_hoy=Carbon::now();
        }else{
            return "fechas no coinciden";
        }
        $fecha_fin=$fecha_hoy->addMinute(5);
        $detalles = new Solicitud_Detalle();
        $foto = new Foto_Solicitud();

        $solicitud = new Solicitud();
        $solicitud->cliente_id = $cliente_id;
        $solicitud->tecnico_id = $tecnico_id;
        $solicitud->fecha_registrado = $fecha_hoy;
        $solicitud->fecha_vencimiento = $fecha_fin;
        $solicitud->estado_id = 1;
        $solicitud->descripcion_servicio = $args['descripcion_servicio'];
        $solicitud->save();

        $detalles = new Solicitud_Detalle();
        $detalles->solicitud_id = $solicitud->id;
        $detalles->habilidades_solicitadas = $args['habilidades_solicitadas'];
        $detalles->save();
    }

}
