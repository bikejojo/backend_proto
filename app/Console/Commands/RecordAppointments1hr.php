<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tecnico;
use App\Models\Servicio;
use App\Jobs\recordAgenda;
use App\Models\Cliente_Interno;
use Illuminate\Console\Command;
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
        $minMinutesThirteen = now()->addMinutes(57);
        $addMinutesThirteen = now()->addMinutes(63);
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
            $technicians = Tecnico::where('id',$service->technicianId)->first();
            $technician = User::where('id',$services->userId)->where('type_user',1)->first();
            if($client){
                $config = DiccionaryNotifications::getByKey('record_client');
                recordAgenda::dispatch($services , $client , $config);
            }
            if($technician){
                $config = DiccionaryNotifications::getByKey('record_technician');
                recordAgenda::dispatch($services , $technician , $config);
            }
        }
        $this->info('Se enviaron los recordatorios correspondientes.');
    }
}
