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
        $minMinutesThirteen = $now->copy()->subSeconds(9)->format('Y-m-d H:i:s');
        $addMinutesThirteen = $now->copy()->addMinutes(11)->format('Y-m-d H:i:s');

        $services = Servicio::where('stateId', 1)
            ->whereBetween('updatedDateTime', [$minMinutesThirteen, $addMinutesThirteen])
            ->where('typeClient', '1')
            ->get();

        if ($services->isEmpty()) {
            $this->info('No hay servicios para enviar recordatorio');
            return;
        }

        foreach ($services as $service) {
            $client = Cliente_Interno::find($service->clientId);
            $clients = User::find($client->userId);
            $technician = Tecnico::find($service->technicalId);
            $technicians = User::find($technician->userId);

            $fecha = Carbon::parse($service->updatedDateTime)->translatedFormat('d \d\e F \a \l\a\s H:i');

            if (!$clients || !$technicians) {
                continue; // 🔥 Seguridad extra
            }

            /** --- PRIMERO CLIENTE AL TÉCNICO --- */
            $configClient = DiccionaryNotifications::getByKey('record_client');
            $configClient['body'] = str_replace('{fecha}', $fecha, $configClient['body']);

            $notifClient = NotificationUser::join('notifications', 'notifications.id', '=', 'notifications_user.notification_id')
                ->where('notifications_user.user_id', $technicians->id)
                ->where('notifications.sender_id', $clients->id)
                ->where('notifications.type', 7)
                ->where('notifications.status', 2)
                ->whereRaw("notifications.data->>'id_service' = ?", [$service->id])
                ->select('notifications_user.*')
                ->first();

            if ($notifClient) {
                if (!$notifClient->is_service_2hr) {
                    $notifClient->is_service_2hr = true;
                    $notifClient->expo_response = json_encode(['data' => ['status' => 'ok']]);
                    $notifClient->save();
                }
            } else {
                // Crear notificación nueva CLIENTE -> TÉCNICO
                $notification = new Notification();
                $notification->action_key = 'record_client';
                $notification->type_users = $technicians->type_user;
                $notification->data = [
                    'id_service' => $service->id,
                    'id_client' => $clients->id,
                    'type_notification' => $configClient['type'],
                ];
                $notification->type = 7;
                $notification->status = 2;
                $notification->title = $configClient['title'];
                $notification->body = $configClient['body'];
                $notification->send_at = now();
                $notification->sender_id = $clients->id;
                $notification->save();

                $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $notification->id;
                $notificationUser->user_id = $technicians->id;
                $notificationUser->type_users = $technicians->type_user;
                $notificationUser->is_service_2hr = false;
                $notificationUser->is_service_1hr = false;
                $notificationUser->created_at = now();
                $notificationUser->save();

                recordAgenda::dispatch($service, $technicians, $configClient);
                $this->info("Recordatorio CLIENTE enviado a {$technicians->firstName}");
            }

            /** --- LUEGO TÉCNICO AL CLIENTE --- */
            $configTech = DiccionaryNotifications::getByKey('record_technician');
            $configTech['body'] = str_replace('{fecha}', $fecha, $configTech['body']);

            $notifTech = NotificationUser::join('notifications', 'notifications.id', '=', 'notifications_user.notification_id')
                ->where('notifications_user.user_id', $clients->id)
                ->where('notifications.sender_id', $technicians->id)
                ->where('notifications.type', 7)
                ->where('notifications.status', 2)
                ->whereRaw("notifications.data->>'id_service' = ?", [$service->id])
                ->select('notifications_user.*')
                ->first();

            if ($notifTech) {
                if (!$notifTech->is_service_2hr) {
                    $notifTech->is_service_2hr = true;
                    $notifTech->expo_response = json_encode(['data' => ['status' => 'ok']]);
                    $notifTech->save();
                }
            } else {
                // Crear notificación nueva TÉCNICO -> CLIENTE
                $notification = new Notification();
                $notification->action_key = 'record_technician';
                $notification->type_users = $clients->type_user;
                $notification->data = [
                    'id_service' => $service->id,
                    'id_technician' => $technicians->id,
                    'type_notification' => $configTech['type'],
                ];
                $notification->type = 7;
                $notification->status = 2;
                $notification->title = $configTech['title'];
                $notification->body = $configTech['body'];
                $notification->send_at = now();
                $notification->sender_id = $technicians->id;
                $notification->save();

                $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $notification->id;
                $notificationUser->user_id = $clients->id;
                $notificationUser->type_users = $clients->type_user;
                $notificationUser->is_service_2hr = false;
                $notificationUser->is_service_1hr = false;
                $notificationUser->created_at = now();
                $notificationUser->save();

                recordAgenda::dispatch($service, $clients, $configTech);
                $this->info("Recordatorio TÉCNICO enviado a {$clients->firstName}");
            }
        }

        $this->info('✅ Se enviaron los recordatorios correctamente.');
        Log::info('✅ Se ejecutó el recordatorio de citas 2hr.');
    }
}
