<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Solicitud;
use App\Models\Tipo_Estado;
use Carbon\Carbon;

class RevisarSolicitudes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solicitudes:revisar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa las solicitudes pendientes y en conversacion, este se actualizara su estado si ha pasado el tiempo de vencimiento';

    /**
     * Execute the console command.
     */

    function __construct() {
        parent::__construct();
    }

    public function handle(){
        $estadoPendiente =1;
        $estadoCanceladoP = 2;
        $estadoConversacion = 3;
        $estadoCanceladoC = 4;
        $now = Carbon::now();
        //
        $solicitudesPendientes = Solicitud::where('estado_id',$estadoPendiente)
        ->where('fecha_tiempo_vencimiento','<=',$now)
        ->get();

        foreach($solicitudesPendientes as $solicitudes){
            $solicitudes->estado_id=$estadoCanceladoP;
            $solicitudes->save(); // cancelado por tiempo
            $this->info('Solicitud ID ' . $solicitudes->id . ' ha sido cancelada por tiempo de espera.');

        }

        $solicitudesConversacion = Solicitud::where('estado_id',$estadoConversacion)
        ->where('fecha_tiempo_vencimiento','<=',$now)
        ->get();

        foreach($solicitudesConversacion as $solicitudes){
            $solicitudes->estado_id=$estadoCanceladoC;
            $solicitudes->save();
            $this->info('Solicitud ID'.$solicitudes->id.'ha sido cancelado por tiempo de ausencia de ambas partes');
        }

    }
    #debe ser dinamico , y no se corra por consola comentario
        #comando omo se debe ejcutar en un servidor
            //* * * * * cd /ruta-a-tu-proyecto && php artisan solicitudes:revisar >> /dev/null 2>&1
}
