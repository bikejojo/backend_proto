<?php

namespace App\Console\Commands;

use App\Jobs\SubcriptionRecord;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Notification;
use Illuminate\Console\Command;
use App\Models\NotificationUser;
use Illuminate\Support\Facades\Log;
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
        $addHours = now()->addMinutes(1446);
        $mensHours = now()->addMinutes(1436);
        $suscription = Technician_subcripcion::where('status',1)
                                            ->whereBetween('endDateSubcription',[$mensHours,$addHours])->get();
        //dd($suscription);
        //$suscription = Technician_subcripcion::whereTime('endDateSubcription',$addHours)->get();
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
            $exists = Notification::where('action_key', 'terminate_suscription')
                    ->where('data', json_encode([
                        'typeNotification' => $config['type'],
                        'id_technician' => $techinician->id,
                        'id_suscription' => $suscriptions->id,
                    ]))
                ->exists();
                //dd($exists);->sabado
            if ($exists) {
                $this->info('Ya se envió la notificación de suscripción a este técnico.');
                continue;
            }
            $notification = new Notification();
                $notification->action_key = 'terminate_suscription';
                $notification->title = $title;
                $notification->body = $body;
                $notification->data = json_encode([
                    'typeNotification' => $config['type'],
                    'id_technician' => $techinician->id,
                    'id_suscription' => $suscriptions->id,
                ]);
                $notification->type = 5;
                $notification->type_users = $user->type_user;
                $notification->send_at = Carbon::now();
                $notification->status = 2;
                $notification->sender_id = $user->id;
            $notification->save();

            $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $notification->id;
                $notificationUser->user_id = $user->id;
                $notificationUser->type_users = $user->type_user;
                $notificationUser->expo_response = null;
            $notificationUser->save();


            SubcriptionRecord::dispatch($devices,$title,$body);
            $this->info("✅ Recordatorio enviado a {$techinician->firstName}");
        }

        $this->info('Se enviaron los recordatorios de suscripciones correspondientes.');
        Log::info('✅ Se ejecutó el recordatorio de sus suscripciones.');
    }
}
