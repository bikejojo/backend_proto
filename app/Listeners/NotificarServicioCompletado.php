<?php

namespace App\Listeners;

use App\Services\DiccionaryNotifications;
use App\Events\ServicioCompletado;
use App\Jobs\SendNotificationJob;
use App\Models\NotificationUser;
use App\Jobs\QualificationTech;
use App\Models\Cliente_Interno;
use App\Models\Tipo_Actividad;
use App\Models\Servicio;
use App\Models\Tecnico;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarServicioCompletado
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

    private function getStringActivity($value):String
    {
        if($value == 1){
            return ' el ';
        }else{
            return ' la ';
        }
    }

    public function handle(ServicioCompletado $event): void
    {
        $serviceC = $event->service;
        $service = Servicio::where('id',$serviceC->id)->first();
        $actionKey = 'services_finish_tech';
        $config = DiccionaryNotifications::getByKey($actionKey);
        $userTech = Tecnico::where('id', $service->technicalId)->first(); //quien manda
        //dd($userTech);
        $userSend = User::where('id', $userTech->userId)->first(); //usuario quien manda
        $userClie = Cliente_Interno::where('id', $service->clientId)->first(); //quien recibe
        $userReceive = User::where('id', $userClie->userId)->first(); //usuario quien recibe
        $actividad = Tipo_Actividad::where('id', $service->activityId)->first();
        $data = [
            'typeNotification' => $config['type'],
            'id_service' => $service->id,
            'type_notification' => $config['type'],
        ];
        $fecha = Carbon::parse($service->finishDateTime_technician);
        $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');

        $notification = new Notification();
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'] . $userTech->firstName . ' ' . $userTech->lastName .' '.
                                'finalizo '. $this->getStringActivity($service->activityId).
                                $actividad->description . ' '. 'de' .' '. $service->titleService.' ' . 'el dia:'.' ' . $completo;
            $notification->data = json_encode($data);
            $notification->type = 2;
            $notification->type_users = $userSend->type_user;
            $notification->send_at = Carbon::now();
            $notification->status = 6;
            $notification->sender_id = $userSend->id;
        $notification->save();

        $notificationsUser = new NotificationUser();
            $notificationsUser->notification_id = $notification->id;
            $notificationsUser->user_id = $userReceive->id;
            $notificationsUser->type_users = $userReceive->type_user;
            $notificationsUser->expo_response = null ;
            $notificationsUser->created_at = Carbon::now();
        $notificationsUser->save();

        SendNotificationJob::dispatch($notification->id, $userReceive->id);
        QualificationTech::dispatch($notification->id,$userReceive->id,$actionKey);
    }


}
