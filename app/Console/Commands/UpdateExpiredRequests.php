<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Solicitud;
use App\Models\Tipo_Estado;
use App\Services\StateCatalog;
use Illuminate\Console\Command;
use App\Services\StatusAssigner;
use App\Jobs\RequestExpiredSystemd;
use App\Services\DiccionaryNotifications;

class UpdateExpiredRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-expired-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update request status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $diccionary = DiccionaryNotifications::getByKey('request_rejected_system');
        $comments = 'El sistema cancelo la solicitud por tiempo de espera.';
        $request = StatusAssigner::ENTITY_REQUEST;
        $cod=5;
        //$timeMinuts = now()->subMinutes(2); -> prueba eb la verficacion del crontab
        $timeMinuts = now()->subMinutes(120);

        $solicitudes = Solicitud::where('stateId',StatusAssigner::PENDING)
                        ->where('status',StateCatalog::STATUS_ACTIVE)
                        ->where('registrationDateTime','<=',$timeMinuts)
                        ->get();

        foreach ($solicitudes as $solicitud){
            StatusAssigner::assignStateRequest($solicitud,Carbon::now(),$request,$comments,$cod);
            RequestExpiredSystemd::dispatch($solicitud,$diccionary);
            $this->info("Solicitud ID {$solicitud->id} actualizada a 'cancelado por destiempo'.");
        }

        $this->info('Actualización completada.');
    }
}
