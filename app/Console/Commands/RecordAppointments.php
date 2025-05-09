<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Servicio;
use App\Jobs\recordAgenda;
use App\Models\Notification;
use App\Models\Cliente_Interno;
use Illuminate\Console\Command;
use App\Models\NotificationUser;
use Illuminate\Support\Facades\Log;
use App\Services\DiccionaryNotifications;
use Illuminate\Support\Facades\DB;


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
        Carbon::setLocale('es');
        $now = Carbon::now()->seconds(0);
        $minMinutesThirteen = $now->copy()->subSeconds(20)->format('Y-m-d H:i:s');
        $addMinutesThirteen = $now->copy()->addMinutes(10)->format('Y-m-d H:i:s');
        //dd($addMinutesThirteen);
        $service = Servicio::where('stateId',1)
                            ->whereBetween('updatedDateTime',[$minMinutesThirteen,$addMinutesThirteen])
                            ->where('typeClient','1')
                            ->get();
       //dd(DB::connection()->getDatabaseName());
        //dd($service);
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
                // Validar si este cliente ya tiene recordatorio enviado
                $existingNotificationUserClient = NotificationUser::join('notifications', 'notifications.id', '=', 'notifications_user.notification_id')
                    ->where('notifications.type',7)
                    ->where('notifications.status',2)
                    ->where('notifications_user.user_id', $client->id)
                    ->where('notifications_user.is_service_2hr', true)
                    ->where('notifications_user.is_service_1hr', false)
                ->count();
                //dd($existingNotificationUserClient);
                //if($existingNotificationUserClient < 1){

                    $controlNotification = new Notification();
                        $controlNotification->action_key = 'record_appointments';
                        $controlNotification->title = 'record_appointments';
                        $controlNotification->body = 'Se enviaron recordatorios de citas';
                        $controlNotification->data = [
                            'minMinutesThirteen' => $minMinutesThirteen,
                            'addMinutesThirteen' => $addMinutesThirteen,
                        ];
                        $controlNotification->type = 7;
                        $controlNotification->status = 2;
                    $controlNotification->save();

                    $notificationClient = new Notification();
                            $notificationClient->action_key = 'record_client';
                            $notificationClient->type_users = $client->type_user;
                            $notificationClient->data = [
                                'id_service' => $services->id,
                                'id_client' => $client->id,
                                'type_notification' => $config['type'],
                            ];
                            $notificationClient->type = 7;
                            $notificationClient->status = 2;
                            $notificationClient->title = $config['title'];
                            $notificationClient->body = $config['body'];
                            $notificationClient->send_at = now();
                            $notificationClient->sender_id = $client->id;
                    $notificationClient->save();

                    $notificationUserClient = new NotificationUser();
                        $notificationUserClient->notification_id = $notificationClient->id;
                        $notificationUserClient->user_id = $client->id;
                        $notificationUserClient->type_users = $client->type_user;
                        $notificationUserClient->is_service_2hr = true;
                        $notificationUserClient->is_service_1hr = false;
                        $notificationUserClient->created_at = now();
                    $notificationUserClient->save();

                    recordAgenda::dispatch($services , $client , $config);
                    $this->info("Recordatorio enviado a {$clients->firstName}");

                //}
            }
            if($technicians){
                $config = DiccionaryNotifications::getByKey('record_technician');
                $config['body'] = str_replace('{fecha}' , $fecha ,$config['body']);

                $existingNotificationUserTech = NotificationUser::where('user_id', $technicians->id)
                    ->where('is_service_2hr', true)
                    ->where('is_service_1hr', false)
                ->count();
                // NUEVA instancia de Notification en cada iteración

                //if($existingNotificationUserTech < 1 ){

                    $controlNotification = new Notification();
                        $controlNotification->action_key = 'record_appointments';
                        $controlNotification->title = 'record_appointments';
                        $controlNotification->body = 'Se enviaron recordatorios de citas';
                        $controlNotification->data = [
                            'minMinutesThirteen' => $minMinutesThirteen,
                            'addMinutesThirteen' => $addMinutesThirteen,
                        ];
                        $controlNotification->type = 7;
                        $controlNotification->status = 2;
                    $controlNotification->save();

                    $notificationTech = new Notification();
                        $notificationTech->action_key = 'record_technician';
                        $notificationTech->type = 7;
                        $notificationTech->status = 2;
                        $notificationTech->data = [
                            'id_service' => $services->id ,
                            'id_technician' => $technicians->id,
                            'type_notification' => $config['type'],
                        ];
                        $notificationTech->type_users = $technicians->type_user;
                        $notificationTech->title = $config['title'];
                        $notificationTech->body = $config['body'];
                        $notificationTech->send_at = now();
                        $notificationTech->sender_id = $technicians->id;
                    $notificationTech->save();

                    $notificationUserTech = new NotificationUser();
                        $notificationUserTech->notification_id = $notificationTech->id;
                        $notificationUserTech->user_id = $technicians->id;
                        $notificationUserTech->type_users = $technicians->type_user;
                        $notificationUserTech->is_service_2hr = true;
                        $notificationUserTech->is_service_1hr = false;
                        $notificationUserTech->created_at = now();
                    $notificationUserTech->save();

                    recordAgenda::dispatch($services , $technicians , $config);
                    $this->info("Recordatorio enviado a {$technicians->firstName}");
                //}
            }
            continue;
        }
        $this->info('Se enviaron los recordatorios correspondientes.');
        Log::info('Se ejecutó el recordatorio de citas 2hr. ✅ ');
    }
}
