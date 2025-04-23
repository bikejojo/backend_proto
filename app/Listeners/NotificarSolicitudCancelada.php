<?php

namespace App\Listeners;

use App\Events\SolicitudCancelada;
use Carbon\Carbon;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente_Interno;
use App\Models\Tecnico;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Services\DiccionaryNotifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarSolicitudCancelada
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
    public function handle(SolicitudCancelada $event): void
    {
        //
        $solicitud = $event->solicitud;
        //dd($solicitud);
        $actionKey = 'request_rejected';
        $config = DiccionaryNotifications::getByKey($actionKey);
        //dd($config);
        $userClient = Cliente_Interno::where('id', $solicitud->clientId)->first(); //quien recibe
        $userReceive = User::where('id', $userClient->userId)->first(); //usuario quien recibe
        $userTechnician = Tecnico::where('id', $solicitud->technicianId)->first(); //quien manda
        $userSend = User::where('id',$userTechnician->userId)->first(); //usuario quien manda
        $data = [
            'full_name' => $userTechnician->firstName . ' ' . $userTechnician->lastName,
            'photo'=>$userTechnician->photo,
            'phonoNumber'=>$userTechnician->phoneNumber,
            'rate' => $userTechnician->average_rating,
            /**------------------------------------ */
            'title'=>$solicitud->titleRequests,
            'serviceDescription'=>$solicitud->requestDescription,
            'visitDate'=>$solicitud->registationDateTime,
            'actividad' => $solicitud->activityId,
            'latitude'=> $solicitud->latitude,
            'longitude'=> $solicitud->longitude,
            'referencia ubicacion' => $solicitud->serviceLocation,
            'estado del servicio' => 4,
            'tipo notificacion' => 1,
            'id' => $solicitud->id,
        ];
        $notification = new Notification();
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'];
            $notification->data = json_encode($data);
            $notification->type = 1;
            $notification->type_users = $userSend->type_user;
            $notification->send_at = Carbon::now();
            $notification->status = 4;
            $notification->sender_id = $userSend->id;

        $notification->save();

        $notificationUser = new NotificationUser();
            $notificationUser->notification_id = $notification->id;
            $notificationUser->user_id = $userReceive->id;
            $notificationUser->type_users = $userReceive->type_user;
            $notificationUser->expo_response = null;
            $notificationUser->created_at = Carbon::now();
        $notificationUser->save();

        SendNotificationJob::dispatch($notification->id, $userReceive->id);
    }
}
