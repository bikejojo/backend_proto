<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Solicitud;
use App\Models\Tipo_Estado;
use Carbon\Carbon;

class RevisarSolicitudesPendientes extends Command
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
    protected $description = 'Revisa las solicitudes pendientes y actualiza su estado si ha pasado el tiempo de vencimiento';

    /**
     * Execute the console command.
     */

    function __construct() {
        parent::__construct();
    }
    public function handle()
    {
        $estadoPendiente =1;
        $estadoCancelado = 3;
        //
        $solicitudes = Solicitud::where('estado_id',$estadoPendiente)->where('fecha_tiempo_vencimiento','<',Carbon::now())
        ->get();

        foreach($solicitudes as $solicitud){
            $solicitud->estado_id=$estadoCancelado;
            //$solicitud->updated_at = Carbon::now();
            //$solicitud->fecha_tiempo_vencimiento = Carbon::now();
            $solicitud->save();
            $this->info('Solicitud ID ' . $solicitud->id . ' ha sido cancelada por tiempo de espera.');

        }
    }
 #debe ser dinamico , y no se corra por consola comentario
            #comando omo se debe ejcutar en un servidor
            //* * * * * cd /ruta-a-tu-proyecto && php artisan solicitudes:revisar >> /dev/null 2>&1
}
