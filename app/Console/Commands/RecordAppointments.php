<?php

namespace App\Console\Commands;

use App\Jobs\recordAgenda;
use Carbon\Carbon;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Jobs\SendNotificationJob;
use App\Models\Cliente_Interno;
use App\Models\User;
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
            $client = User::where('id',$clients->userId)->where('type_user',2)->first();
            $technician = Tecnico::where('id',$services->technicianId)->first();
            $technicians = User::where('id',$technician->userId)->where('type_user',1)->first();
            if($client){
                $config = DiccionaryNotifications::getByKey('record_client');
                recordAgenda::dispatch($services , $client , $config);
            }
            if($technicians){
                $config = DiccionaryNotifications::getByKey('record_technician');
                recordAgenda::dispatch($services , $technicians , $config);
            }
        }
        $this->info('Se enviaron los recordatorios correspondientes.');
    }
}
