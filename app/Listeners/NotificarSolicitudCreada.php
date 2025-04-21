<?php

namespace App\Listeners;

use App\Events\SolicitudCreada;
use App\Jobs\SendNotificationJob;
use App\Models\Cliente_Interno;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\NotificationsDevice;
use App\Models\DevicesUser;
use App\Models\Devices;
use Illuminate\Support\Facades\DB;
use App\Models\Tecnico;
use App\Models\User;

use App\Services\DiccionaryNotifications;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarSolicitudCreada
{
    /**
     * Create the event listener.
     */

    protected $now;

    public function __construct()
    {
        //
        $this->now = Carbon::now()->format('Ymd_His');
    }

    /**
     * Handle the event.
     */
    public function handle(SolicitudCreada $event)
    {
        //
        $solicitud = $event->solicitud;
        $actionKey = 'request_sent';
        $config = DiccionaryNotifications::getByKey($actionKey);
        $userClient = Cliente_Interno::where('id',$solicitud->clientId)->first();
        $user = User::where('id',$userClient->user_id)->first();
        $data = [
            'full_name' => Tecnico::where('userId',$solicitud->technicianId)->select(DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) As full_name'))->first(),
            'rate' => Tecnico::where('userId',$solicitud->technicianId)->select('average_rating')->first(),
            'actividad' => $solicitud->activityId ,
            'ubicacion' => 'lat:' . $solicitud->latitude . ' ' . 'lng:' . $solicitud->longitude,
            'referencia ubicacion' => $solicitud->serviceLocation,
            'estado del servicio' => $config['type'],
            'id' => $solicitud->id,
        ];

        $notification = new Notification();
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'];
            $notification->data = json_encode($data);
            $notification->type = $user->type_user;
            $notification->send_at = $this->now;
            $notification->status = 1;
            $notification->sender_id = $user->id;
            $notification->save();
        $notificationsUsers = new NotificationUser();
            $notificationsUsers;
            $notificationsUsers;
            $notificationsUsers;
            $notificationsUsers;
    }
}
