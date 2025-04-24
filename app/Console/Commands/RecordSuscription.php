<?php

namespace App\Console\Commands;

use App\Jobs\SubcriptionRecord;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Devices;
use App\Models\DevicesUser;
use Illuminate\Console\Command;
use App\Models\Technician_subcripcion;
use App\Services\DiccionaryNotifications;


class RecordSuscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:record-suscription';

    protected $description = 'Envio de recordatorios de suscripciones apunto de expirar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Carbon::setLocale('es');
        $addHours = now()->addMinutes(1440);
        $suscription = Technician_subcripcion::whereBetween('endDateSubcription',$addHours)->get();
        $config = DiccionaryNotifications::getByKey('terminate_suscription');

        foreach($suscription as $suscriptions){

            $fecha = Carbon::parse($suscriptions->endDateSubcription)->translatedFormat('d \d\e F');
            $techinician = Tecnico::where('id',$suscriptions->technicianId)->first();
            $user = User::where('id',$techinician->userId)->first();
            $devicesUser = DevicesUser::where('users_id',$user->id)->first();
            $devices = Devices::where('id',$devicesUser->device_id)->first();
            $body = str_replace('{fecha}', $fecha, $config['body']);
            $title = $config['title'];

            if(!$techinician || !$user || !$devicesUser || !$devices){
                $this->info('No se encontraron los datos del tecnico o usuario o dispositivo.');
                continue;
            }
            SubcriptionRecord::dispatch($devices,$title,$body);
        }
    }
}
