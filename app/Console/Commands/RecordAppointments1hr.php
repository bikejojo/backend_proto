<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Servicio;
use App\Jobs\recordAgenda;
use App\Models\Cliente_Interno;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\DiccionaryNotifications;


class RecordAppointments1hr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:record-appointments1hr';
    protected $description = 'Se enviara notifcaciones de recordatorio del servicio en 1 hora antes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Carbon::setLocale('es');
        $minMinutesThirteen = now()->addMinutes(57);
        $addMinutesThirteen = now()->addMinutes(65);
        $service = Servicio::where('stateId',1)
                            ->whereBetween('updatedDateTime',[$minMinutesThirteen , $addMinutesThirteen])
                            ->get();

        if($service->isEmpty()){
            $this->info('No hay servicios para enviar recordatorio');
            return;
        };

        foreach($service as $services){
            $clients = Cliente_Interno::where('id', $services->clientId)->first();
            $client = User::where('id',$clients->userId)->first();
            $technicians = Tecnico::where('id',$services->technicalId)->first();
            $technician = User::where('id',$technicians->userId)->first();
            $fecha = Carbon::parse($services->updatedDateTime)->translatedFormat('d \d\e F \a \l\a\s H:i');

            if($client){
                $config = DiccionaryNotifications::getByKey('record_client');
                $config['body'] = str_replace('{fecha}' , $fecha ,$config['body']);
                recordAgenda::dispatch($services , $client , $config);
                $this->info("Recordatorio enviado a {$clients->firstName}");
            }
            if($technician){
                $config = DiccionaryNotifications::getByKey('record_technician');
                $config['body'] = str_replace('{fecha}' , $fecha ,$config['body']);
                recordAgenda::dispatch($services , $technician , $config);
                $this->info("Recordatorio enviado a {$technicians->firstName}");
            }
        }
        $this->info('Se enviaron los recordatorios correspondientes.');
        Log::info('✅ Se ejecutó el recordatorio de citas 1hr.');
    }
}
