<?php

namespace App\Listeners;

use App\Services\DiccionaryNotifications;
use App\Jobs\SendNotificationJob;
use App\Models\NotificationUser;
use App\Models\Cliente_Interno;
use App\Events\ServicioAnulado;

use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarServicioAnulado
{
    /**
     * Create the event listener.
     */
    protected $now;

    public function __construct()
    {
        Carbon::setLocale('es');
        $this->now = Carbon::now();
    }


    /**
     * Handle the event.
     */
    public function handle(ServicioAnulado $event)
    {
        if($event->key === 'services_anull_client'){

            $service = $event->service;
            $actionsKey = 'services_anull_client';
            $config = DiccionaryNotifications::getByKey($actionsKey);
            $userClie = Cliente_Interno::where('id', $service->clientId)->first(); //quien manda
            $userSend = User::where('id', $userClie->userId)->first(); //usuario quien mand
            $userTech = Tecnico::where('id', $service->technicalId)->first(); //quien recibe

            $userReceive = User::where('id', $userTech->userId)->first(); //usuario quien recibe
            $data = [
                'typeNotification' => $config['type'],
                'id_service' => $service->id,
                'type_notification' => $config['type'],
                'full_name' => $userTech->firstName . ' ' . $userTech->lastName,
                'rate' => $userTech->rate,
                'actividad' => $service->activityId,
                'ubicacion' => 'lat: ' . $service->latitude . ' ' . 'lng: ' . $service->longitude,
                'referencia_ubicacion' => $service->serviceLocation,
                'estado_del_servicio' => $service->stateId,
                'id_request' => $service->requestsId
            ];
            $fecha = Carbon::parse($service->updatedDateTime);
            $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');
            $notification = new Notification();
                $notification->action_key = $actionsKey;
                $notification->title = $config['title'];
                $notification->body = $config['body'] . $completo;
                $notification->data = $data;
                $notification->type = 2;
                $notification->type_users = $userSend->type_user;
                $notification->send_at = Carbon::now();
                $notification->status = 5;
                $notification->sender_id = $userSend->id;
            $notification->save();

            $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $notification->id;
                $notificationUser->user_id = $userReceive->id;
                $notificationUser->type_users = $userReceive->type_user;
                $notificationUser->expo_response = null ;
                $notificationUser->created_at = Carbon::now();
            $notificationUser->save();

        }elseif ( $event->key === 'serv_anull_client') {
            $service = $event->service;
            $actionsKey = 'serv_anull_client';
            $config = DiccionaryNotifications::getByKey($actionsKey);
            $userClie = Cliente_Interno::where('id', $service->clientId)->first(); //quien manda
            $userSend = User::where('id', $userClie->userId)->first(); //usuario quien mand
            $userTech = Cliente_Interno::where('id', $service->clientId)->first(); //quien recibe
            $userReceive = User::where('id', $userTech->userId)->first(); //usuario quien recibe
            $data = [
                'typeNotification' => $config['type'],
                'id_service' => $service->id,
                'type_notification' => $config['type'],
                'full_name' => $userTech->firstName . ' ' . $userTech->lastName,
                'rate' => $userTech->rate,
                'id_technician' => $userTech['id'],
                'id_client' => $userClie['id'],
                'actividad' => $service->activityId,
                'ubicacion' => 'lat: ' . $service->latitude . ' ' . 'lng: ' . $service->longitude,
                'referencia_ubicacion' => $service->serviceLocation,
                'estado_del_servicio' => $service->stateId,
                'id_request' => $service->requestsId
            ];
            $fecha = Carbon::parse($service->updatedDateTime);
            $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');
            $notification = new Notification();
                $notification->action_key = $actionsKey;
                $notification->title = $config['title'];
                $notification->body = $config['body'];
                $notification->data = $data;
                $notification->type = 2;
                $notification->type_users = $userSend->type_user;
                $notification->send_at = Carbon::now();
                $notification->status = 5;
                $notification->sender_id = $userSend->id;
            $notification->save();

            $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $notification->id;
                $notificationUser->user_id = $userReceive->id;
                $notificationUser->type_users = $userReceive->type_user;
                $notificationUser->expo_response = null ;
                $notificationUser->created_at = Carbon::now();
            $notificationUser->save();
        }else{
            $service = $event->service;
            $actionsKey = 'services_anull_tech';
            $config = DiccionaryNotifications::getByKey($actionsKey);
            $userTech = Tecnico::where('id', $service->technicalId)->first();//usuario quien manda
            $userSend = User::where('id', $userTech->userId)->first();//quien manda

            $userClie = Cliente_Interno::where('id', $service->clientId)->first();//quien recibe
            $userReceive = User::where('id', $userClie->userId)->first(); //usuario quien recibe
            $data = [
                'typeNotification' => $config['type'],
                'id_service' => $service->id,
                'type_notification' => $config['type'],
                'id_technician' => $userTech['id'],
                'id_client' => $userClie['id'],
                'full_name' => $userTech->firstName . ' ' . $userTech->lastName,
                'photo' => $userTech->photo,
                'rate' => $userTech->rate,
                'actividad' => $service->activityId,
                'ubicacion' => 'lat: ' . $service->latitude . ' ' . 'lng: ' . $service->longitude,
                'referencia_ubicacion' => $service->serviceLocation,
                'estado_del_servicio' => $service->stateId,
                'id_request' => $service->requestsId,

            ];
            $fecha = Carbon::parse($service->updatedDateTime);
            $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');
            $notification = new Notification();
                $notification->action_key = $actionsKey;
                $notification->title = $config['title'];
                $notification->body = $config['body'] . $completo;
                $notification->data = $data;
                $notification->type = 2;
                $notification->type_users = $userSend->type_user;
                $notification->send_at = Carbon::now();
                $notification->status = 5;
                $notification->sender_id = $userSend->id;
            $notification->save();
            $data['id'] = $notification->id;
            $notification->data = $data;
            $notification->save();
            $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $notification->id;
                $notificationUser->user_id = $userReceive->id;
                $notificationUser->type_users = $userReceive->type_user;
                $notificationUser->expo_response = null ;
                $notificationUser->created_at = Carbon::now();
            $notificationUser->save();

        }

        SendNotificationJob::dispatch($notification->id, $userReceive->id);
    }
}
