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
        $now = Carbon::now()->seconds(0);
        $minMinutesThirteen = $now->copy()->subSeconds(60)->format('Y-m-d H:i:s');
        $addMinutesThirteen = $now->copy()->addMinutes(58)->format('Y-m-d H:i:s');

        $services = Servicio::where('stateId',1)
                            ->whereBetween('updatedDateTime',[$minMinutesThirteen , $addMinutesThirteen])
                            ->where('typeClient', '1')
                            ->get();

        if($services->isEmpty()){
            $this->info('No hay servicios para enviar recordatorio');
            return;
        };

        foreach($services as $service){
            $client = Cliente_Interno::find($service->clientId);
            $clients = User::find($client->userId);
            $technician = Tecnico::find($service->technicalId);
            $technicians = User::find($technician->userId);

            $fecha = Carbon::parse($service->updatedDateTime)->translatedFormat('d \d\e F \a \l\a\s H:i');

            if($clients){
                $config = DiccionaryNotifications::getByKey('record_client');
                $config['body'] = str_replace('{fecha}', $fecha, $config['body']);

                $notif = NotificationUser::join('notifications', 'notifications.id', '=', 'notifications_user.notification_id')
                    ->where('notifications_user.user_id', $technicians->id )
                    ->where('notifications.sender_id', $clients->id)
                    ->where('notifications.type',7)
                    ->where('notifications.status',2)
                    ->whereRaw("notifications.data->>'id_service' = ?", [$service->id])
                    ->select('notifications_user.*')
                ->first();

                if ($notif && !$notif->is_service_1hr) {
                    $notif->is_service_1hr = true;
                    $notif->expo_response = json_encode(['data' => ['status' => 'ok']]);
                    $notif->save();
                }else{
                    $notificationClient = new Notification();
                        $notificationClient->action_key = 'record_client';
                        $notificationClient->type_users = $technicians->type_user;
                        $notificationClient->data = [
                            'id_service' => $service->id,
                            'id_client' => $clients->id,
                            'type_notification' => $config['type'],
                        ];
                        $notificationClient->type = 7;
                        $notificationClient->status = 2;
                        $notificationClient->title = $config['title'];
                        $notificationClient->body = $config['body'];
                        $notificationClient->send_at = now();
                        $notificationClient->sender_id = $clients->id;
                    $notificationClient->save();
                    //dd($notificationClient);
                    $notificationUserClient = new NotificationUser();
                        $notificationUserClient->notification_id = $notificationClient->id;
                        $notificationUserClient->user_id = $technicians->id;
                        $notificationUserClient->type_users = $clients->type_user;
                        $notificationUserClient->is_service_2hr = false;
                        $notificationUserClient->is_service_1hr = false;
                        $notificationUserClient->created_at = now();
                    $notificationUserClient->save();

                    recordAgenda::dispatch($service, $client, $config);
                    $this->info("Recordatorio enviado a {$clients->firstName}");
                }

            }

            if($technicians){
                $config = DiccionaryNotifications::getByKey('record_technician');
                $config['body'] = str_replace('{fecha}', $fecha, $config['body']);

                $notif = NotificationUser::join('notifications', 'notifications.id', '=', 'notifications_user.notification_id')
                    ->where('notifications_user.user_id', $clients->id)
                    ->where('notifications.sender_id', $technicians->id)
                    ->where('notifications.type',7)
                    ->where('notifications.status',2)
                    ->whereRaw("notifications.data->>'id_service' = ?", [$service->id])
                    ->select('notifications_user.*')
                    ->first();
                 if ($notif && !$notif->is_service_1hr) {
                    $notif->is_service_1hr = true;
                    $notif->expo_response = json_encode(['data' => ['status' => 'ok']]);
                    $notif->save();
                }else{
                     $notificationTech = new Notification();
                        $notificationTech->action_key = 'record_technician';
                        $notificationTech->type = 7;
                        $notificationTech->status = 2;
                        $notificationTech->data = [
                            'id_service' => $service->id,
                            'id_technician' => $technicians->id,
                            'type_notification' => $config['type'],
                        ];
                        $notificationTech->type_users = $clients->type_user;
                        $notificationTech->title = $config['title'];
                        $notificationTech->body = $config['body'];
                        $notificationTech->send_at = now();
                        $notificationTech->sender_id = $technicians->id;
                    $notificationTech->save();

                    $notificationUserTech = new NotificationUser();
                        $notificationUserTech->notification_id = $notificationTech->id;
                        $notificationUserTech->user_id = $clients->id;
                        $notificationUserTech->type_users = $technicians->type_user;
                        $notificationUserTech->is_service_2hr = false;
                        $notificationUserTech->is_service_1hr = false;
                        $notificationUserTech->created_at = now();
                    $notificationUserTech->save();

                    recordAgenda::dispatch($service, $technicians, $config);
                    $this->info("Recordatorio enviado a {$technicians->firstName}");
                }
            }
        }
        $this->info('Se enviaron los recordatorios correspondientes.');
        Log::info('Se ejecutó el recordatorio de citas 2hr. ✅');
    }
}
