<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Events\ServicioCalificacion;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Cliente_Interno;
use App\Services\DiccionaryNotifications;
use App\Models\Tipo_Actividad;
use App\Models\Servicio;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Devices;
use App\Models\DevicesUser;

class NotificarServicioCalificacion
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
    public function handle(ServicioCalificacion $event): void
    {
        $service = $event->service;
        $serviceC = Servicio::where('id',$service->id)->first();
        $actionKey = $event->message;//'qualification_done'; //1234567890
        //dd($actionKey);
        if( $actionKey === 'qualification_done'){
            $config = DiccionaryNotifications::getByKey($actionKey);

            $userClient = Cliente_Interno::where('id', $serviceC->clientId)->first(); //quien manda
            $userSend = User::where('id', $userClient->userId)->first(); //usuario quien manda
            $userTech = Tecnico::where('id', $serviceC->technicalId)->first(); //quien recibe
            $userReceive = User::where('id', $userTech->userId)->first(); //usuario quien recibe
            $nameActividad = Tipo_Actividad::where('id',$serviceC->activityId)->value('description');
            $fecha = Carbon::parse($serviceC->finishDateTime_technician);
            $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');

            $data = [
                'typeNotification' => $config['type'],
                'id_service' => $serviceC->id,
                'type_notification' => $config['type'],
                'full_name' => $userTech->firstName . ' ' . $userTech->lastName,
                'photo' => $userTech->photo,
                'rate' => $userTech->average_rating,
                'id_technician' => $userTech['id'],
                    'id_client' => $userClient['id'],
                'actividad' => $nameActividad,
                'ubicacion' => 'lat: ' . $serviceC->latitude . ' ' . 'lng: ' . $serviceC->longitude,
                'referencia_ubicacion' => $serviceC->serviceLocation,
                'estado_del_servicio' => $serviceC->stateId,
                'id_request' => $serviceC->requestsId,
                'description' => $serviceC->serviceDescription,
                'date_service' => $serviceC->updatedDateTime,
            ];

            $notification = new Notification();
                $notification->action_key = $actionKey;
                $notification->title = $config['title'];
                $notification->body = $config['body'] . $completo;
                $notification->data = $data;
                $notification->type = 2;
                $notification->type_users = $userSend->type_user;
                $notification->send_at = Carbon::now();
                $notification->status = 7;
                $notification->sender_id = $userSend->id;
            $notification->save();
            $data['id'] = $notification->id;
            $notification->data = $data;
            $notification->save();
            $notificationsUser = new NotificationUser();
                $notificationsUser->notification_id = $notification->id;
                $notificationsUser->user_id = $userReceive->id;
                $notificationsUser->type_users = $userReceive->type_user;
            $notificationsUser->save();

            $devicesUser = DevicesUser::where('users_id',$userReceive->id)->first();
            $devices = Devices::where('id',$devicesUser->device_id)->first();
            $response = Http::post('https://exp.host/--/api/v2/push/send',[
                        'to' => $devices->expo_token,
                        'title' => $config['title'],
                        'body' => $config['body'],
                        'data' => $notification->data,
                    ]);
            $NotificationUser = NotificationUser::where('notification_id',$notification->id)->first();
            $NotificationUser->expo_response = json_encode($response->json());
            $NotificationUser->user_id = $userReceive->id;
            $NotificationUser->save();
        }else{
            //dd();
            $config = DiccionaryNotifications::getByKey($actionKey);

            $userClient = Cliente_Interno::where('id', $serviceC->clientId)->first(); //quien manda
            $userSend = User::where('id', $userClient->userId)->first(); //usuario quien manda
            $userTech = Tecnico::where('id', $serviceC->technicalId)->first(); //quien recibe
            $userReceive = User::where('id', $userTech->userId)->first(); //usuario quien recibe
            $nameActividad = Tipo_Actividad::where('id',$serviceC->activityId)->value('description');
            $fecha = Carbon::parse($serviceC->finishDateTime_technician);
            $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');

            $data = [
                'typeNotification' => $config['type'],
                'id_service' => $serviceC->id,
                'type_notification' => $config['type'],
                'full_name' => $userTech->firstName . ' ' . $userTech->lastName,
                'photo' => $userTech->photo,
                'rate' => $userTech->average_rating,
                'id_technician' => $userTech['id'],
                    'id_client' => $userClient['id'],
                'actividad' => $nameActividad,
                'ubicacion' => 'lat: ' . $serviceC->latitude . ' ' . 'lng: ' . $serviceC->longitude,
                'referencia_ubicacion' => $serviceC->serviceLocation,
                'estado_del_servicio' => $serviceC->stateId,
                'id_request' => $serviceC->requestsId,
                'description' => $serviceC->serviceDescription,
                'date_service' => $serviceC->updatedDateTime,
            ];

            $notification = new Notification();
                $notification->action_key = $actionKey;
                $notification->title = $config['title'];
                $notification->body = $config['body'] . $completo;
                $notification->data = $data;
                $notification->type = 2;
                $notification->type_users =  $userReceive->type_user;
                $notification->send_at = Carbon::now();
                $notification->status = 7;
                $notification->sender_id = $userReceive->id;
            $notification->save();
                $data['id'] = $notification->id;
                $notification->data = $data;
            $notification->save();
             $notificationsUser = new NotificationUser();
                $notificationsUser->notification_id = $notification->id;
                $notificationsUser->user_id = $userSend->id;
                $notificationsUser->type_users =$userSend->type_user;
            $notificationsUser->save();

            $devicesUser = DevicesUser::where('users_id',$userSend->id)->first();
            $devices = Devices::where('id',$devicesUser->device_id)->first();
            $response = Http::post('https://exp.host/--/api/v2/push/send',[
                        'to' => $devices->expo_token,
                        'title' => $config['title'],
                        'body' => $config['body'],
                        'data' => $notification->data,
                    ]);
            $NotificationUser = NotificationUser::where('notification_id',$notification->id)->first();
                $NotificationUser->expo_response = json_encode($response->json());
            $NotificationUser->save();
        }
        //SendNotificationJob::dispatch($notification->id, $userReceive->id);
    }
}
