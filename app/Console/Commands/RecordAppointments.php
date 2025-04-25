<?php

namespace App\Console\Commands;

use App\Jobs\recordAgenda;
use Carbon\Carbon;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Jobs\SendNotificationJob;
use App\Models\Cliente_Interno;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Services\DiccionaryNotifications;
use Illuminate\Console\Command;

class RecordAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:record-appointments';
    protected $description = 'Envio de recortarios de agenda de servicios';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $minMinutesThirteen = now()->addMinutes(118);
        $addMinutesThirteen = now()->addMinutes(122);
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
            $technician = Tecnico::where('id',$services->technicalId)->first();
            $technicians = User::where('id',$technician->userId)->first();

            $fecha = Carbon::parse($services->updatedDateTime)->translatedFormat('d \d\e F \a \l\a\s H:i');
           
            if($client){
                $config = DiccionaryNotifications::getByKey('record_client');
                $config['body'] = str_replace('{fecha}' , $fecha ,$config['body']);
                recordAgenda::dispatch($services , $client , $config);
                $this->info("Recordatorio enviado a {$clients->firstName}");
            }
            if($technicians){
                $config = DiccionaryNotifications::getByKey('record_technician');
                $config['body'] = str_replace('{fecha}' , $fecha ,$config['body']);
                recordAgenda::dispatch($services , $technicians , $config);
                $this->info("Recordatorio enviado a {$technician->firstName}");
            }
        }
        $this->info('Se enviaron los recordatorios correspondientes.');
        Log::info('✅ Se ejecutó el recordatorio de citas 2hr.');
    }
}
