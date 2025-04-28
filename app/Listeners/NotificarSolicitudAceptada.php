<?php

namespace App\Listeners;

use App\Events\SolicitudAceptada;
use App\Jobs\SendNotificationJob;
use App\Models\Cliente_Interno;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Tecnico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\DiccionaryNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarSolicitudAceptada
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
    public function handle(SolicitudAceptada $event): void
    {
        $service = $event->service;
        $actionKey = 'request_accepted';
        $config = DiccionaryNotifications::getByKey($actionKey);
        $userTech = Tecnico::where('id', $service->technicalId)->first(); //quien manda
        $userSend = User::where('id', $userTech->userId)->first(); //usuario quien manda

        $userClie = Cliente_Interno::where('id', $service->clientId)->first(); //quien recibe
        $userReceive = User::where('id', $userClie->userId)->first(); //usuario quien recibe

        $data = [
            'typeNotification' => $config['type'],
            'id_service' => $service->id,
            'type_notification' => $config['type'],
        ];
        $fecha = Carbon::parse($service->updatedDateTime);
        $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');
        //dd($completo);
        $notification = new Notification();
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'] . $completo;
            $notification->data = json_encode($data);
            $notification->type = 1;
            $notification->type_users = $userSend->type_user;
            $notification->send_at = Carbon::now();
            $notification->status = 3;
            $notification->sender_id = $userSend->id;
        $notification->save();

        $notificationUser = new NotificationUser();
            $notificationUser->notification_id = $notification->id;
            $notificationUser->user_id = $userReceive->id;
            $notificationUser->type_users = $userReceive->type_user;
            $notificationUser->expo_response = null ;
            $notificationUser->created_at = Carbon::now();
        $notificationUser->save();

        SendNotificationJob::dispatch($notification->id, $userReceive->id);
    }
}
