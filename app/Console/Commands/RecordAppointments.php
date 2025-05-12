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
    protected $signature = 'app:record-appointments';
    protected $description = 'Envío de recordatorios de agenda de servicios';

    public function handle()
    {
        Carbon::setLocale('es');
        $now = Carbon::now()->seconds(0);
        $minMinutesThirteen = $now->copy()->subSeconds(20)->format('Y-m-d H:i:s');
        $addMinutesThirteen = $now->copy()->addMinutes(10)->format('Y-m-d H:i:s');

        $services = Servicio::where('stateId', 1)
            ->whereBetween('updatedDateTime', [$minMinutesThirteen, $addMinutesThirteen])
            ->where('typeClient', '1')
            ->get();

        if ($services->isEmpty()) {
            $this->info('No hay servicios para enviar recordatorio');
            return;
        }

        foreach ($services as $service) {
            $clients = Cliente_Interno::find($service->clientId);
            $client = User::find($clients->userId);
            $technician = Tecnico::find($service->technicalId);
            $technicians = User::find($technician->userId);

            $fecha = Carbon::parse($service->updatedDateTime)->translatedFormat('d \d\e F \a \l\a\s H:i');

            // ----- CLIENTE -----
            if ($client) {
                $config = DiccionaryNotifications::getByKey('record_client');
                $config['body'] = str_replace('{fecha}', $fecha, $config['body']);

                $notif = NotificationUser::join('notifications', 'notifications.id', '=', 'notifications_user.notification_id')
                    ->where('notifications_user.user_id', $client->id)
                    ->where('notifications.sender_id', $technicians->id)
                    ->whereRaw("notifications.data->>'id_service' = ?", [(string) $service->id])
                    ->select('notifications_user.*')
                    ->first();

                if ($notif && $notif->is_service_2hr) continue;

                if ($notif && !$notif->is_service_2hr) {
                    $notif->is_service_2hr = true;
                    $notif->expo_response = json_encode(['data' => ['status' => 'ok']]);
                    $notif->save();
                    continue;
                }

                $notificationClient = new Notification();
                $notificationClient->action_key = 'record_client';
                $notificationClient->type_users = $client->type_user;
                $notificationClient->data = [
                    'id_service' => (string) $service->id,
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

                recordAgenda::dispatch($service, $client, $config);
                $this->info("Recordatorio enviado a {$clients->firstName}");
            }

            // ----- TÉCNICO -----
            if ($technicians) {
                $config = DiccionaryNotifications::getByKey('record_technician');
                $config['body'] = str_replace('{fecha}', $fecha, $config['body']);

                $notif = NotificationUser::join('notifications', 'notifications.id', '=', 'notifications_user.notification_id')
                    ->where('notifications_user.user_id', $technicians->id)
                    ->where('notifications.sender_id', $client->id)
                    ->whereRaw("notifications.data->>'id_service' = ?", [(string) $service->id])
                    ->select('notifications_user.*')
                    ->first();

                if ($notif && $notif->is_service_2hr) continue;

                if ($notif && !$notif->is_service_2hr) {
                    $notif->is_service_2hr = true;
                    $notif->expo_response = json_encode(['data' => ['status' => 'ok']]);
                    $notif->save();
                    continue;
                }

                $notificationTech = new Notification();
                $notificationTech->action_key = 'record_technician';
                $notificationTech->type = 7;
                $notificationTech->status = 2;
                $notificationTech->data = [
                    'id_service' => (string) $service->id,
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

                recordAgenda::dispatch($service, $technicians, $config);
                $this->info("Recordatorio enviado a {$technicians->firstName}");
            }
        }

        $this->info('Se enviaron los recordatorios correspondientes.');
        Log::info('Se ejecutó el recordatorio de citas 2hr. ✅');
    }
}
