<?php

namespace App\Listeners;

use App\Services\DiccionaryNotifications;
use App\Events\ServicioCompletado;
use App\Jobs\QualificationTech;
use App\Jobs\SendNotificationJob;
use App\Models\NotificationUser;
use App\Models\Cliente_Interno;
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
    public function handle(ServicioCompletado $event): void
    {
        $service = $event->service;
        $actionKey = 'services_finish_tech';
        $config = DiccionaryNotifications::getByKey($actionKey);
        $userTech = Tecnico::where('id', $service->technicalId)->first(); //quien manda
        $userSend = User::where('id', $userTech->userId)->first(); //usuario quien manda
        $userClie = Cliente_Interno::where('id', $service->clientId)->first(); //quien recibe
        $userReceive = User::where('id', $userClie->userId)->first(); //usuario quien recibe

        $data = [
            'typeNotification' => $config['type'],
            'id_service' => $service->id,
        ];
        $fecha = Carbon::parse($service->updatedDateTime);
        $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');

        $notification = new Notification();
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'] . $completo;
            $notification->data = json_encode($data);
            $notification->type = 2;
            $notification->type_users = $userSend->type_user;
            $notification->send_at = Carbon::now();
            $notification->status = 5;
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
        QualificationTech::dispatch($notification->id,$userReceive->id);
    }
}
