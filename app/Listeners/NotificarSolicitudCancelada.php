<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tecnico;
use App\Models\Notification;
use App\Models\Tipo_Actividad;
use App\Models\Cliente_Interno;
use App\Models\NotificationUser;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Events\SolicitudCancelada;
use App\Services\DiccionaryNotifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use function Ramsey\Uuid\v1;

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
    public function handle(SolicitudCancelada $event)
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
        $nameActividad = Tipo_Actividad::where('id',$solicitud->activityId)->value('description');
        $data = [
            'typeNotification' => $config['type'],
            /**---------------------------------- */
            'full_name' => $userTechnician->firstName . ' ' . $userTechnician->lastName,
            'photo'=>$userTechnician->photo,
            'phonoNumber'=>$userTechnician->phoneNumber,
            'rate' => $userTechnician->average_rating,
            /**------------------------------------ */
            'id_technician' => $userTechnician['id'],
                'id_client' => $userClient['id'],
            'title'=>$solicitud->titleRequests,
            'serviceDescription'=>$solicitud->requestDescription,
            'visitDate'=>$solicitud->registrationDateTime,
            'actividad' => $nameActividad,
            'ubicacion'=> 'lat: '.$solicitud->latitude . ' ' . 'lon: ' . $solicitud->longitude,
            'referencia_ubicacion' => $solicitud->serviceLocation,
            'estado_del_servicio' => 4,
            'tipo_notificacion' => 1,
            'id_request' => $solicitud->id,
            'description' => $solicitud->requestDescription,
            'date_request' => $solicitud->registrationDateTime,
        ];
        $fecha = Carbon::parse($solicitud->registrationDateTime);
        $completo = $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s H:i');
        $notification = new Notification();
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'];//str_replace('{fecha}', $completo, $config['body']);;
            $notification->data = $data;
            $notification->type = 1;
            $notification->type_users = $userSend->type_user;
            $notification->send_at = Carbon::now();
            $notification->status = 4;
            $notification->sender_id = $userSend->id;

        $notification->save();
        $data['id'] = $notification->id;
        $notification->data = $data;
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
