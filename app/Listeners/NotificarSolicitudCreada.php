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
        //dd($solicitud);
        $actionKey = 'request_sent';
        $config = DiccionaryNotifications::getByKey($actionKey);
        $userClient = Cliente_Interno::where('id',$solicitud->clientId)->first();
        $user = User::where('id',$userClient->userId)->first();
        $data = [
            'full_name' => Tecnico::where('id',$solicitud->technicianId)->select(DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) As full_name'))->first(),
            'rate' => Tecnico::where('id',$solicitud->technicianId)->select('average_rating')->first(),
            'actividad' => $solicitud->activityId ,
            'ubicacion' => 'lat:' . $solicitud->latitude . ' ' . 'lng:' . $solicitud->longitude,
            'referencia ubicacion' => $solicitud->serviceLocation,
            'estado del servicio' => $config['type'],
            'id' => $solicitud->id,
        ];

        $notification = new Notification();
        //dd($user);
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'];
            $notification->data = json_encode($data);
            $notification->type = 'solicitud';
            $notification->type_users = $user->type_user;
            $notification->send_at = Carbon::now();
            $notification->status ='enviada';
            $notification->sender_id = $user->id;
            $notification->save();
        $userRecept = Tecnico::find($solicitud->technicianId);
        $userReceive = User::where('id',$userRecept->userId)->first();

        $device = DevicesUser::where('users_id',$userRecept->userId)->first();
        $deviceUser = Devices::where('id',$device->device_id)->first();

        $notificationsDevice = new NotificationsDevice();
            $notificationsDevice->user_id = $userReceive->id;
            $notificationsDevice->device_id = $deviceUser->id;
            $notificationsDevice->expo_token = $deviceUser->expo_token;
            $notificationsDevice->is_active = true;
        $notificationsDevice->save();

        $notificationsUsers = new NotificationUser();
            $notificationsUsers->created_at = Carbon::now();
            $notificationsUsers->expo_response = $deviceUser->expo_token;
            $notificationsUsers->user_id = $userReceive->id;
            $notificationsUsers->type_users = $userReceive->type_user;
            $notificationsUsers->notification_id = $notification->id;
        $notificationsUsers->save();

        SendNotificationJob::dispatch($notification->id,$userReceive->id);
        //--------------------------- notificaciones    id de receptor;
    }
}
