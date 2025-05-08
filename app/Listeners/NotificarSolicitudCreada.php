<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Devices;
use App\Models\Tecnico;
use App\Models\DevicesUser;
use App\Models\Notification;
use App\Models\Tipo_Actividad;
use App\Events\SolicitudCreada;
use App\Models\Cliente_Interno;
use App\Models\NotificationUser;
use App\Jobs\SendNotificationJob;
use App\Services\DiccionaryNotifications;
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
        Carbon::setLocale('es');
        $this->now = Carbon::now();
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
        $userTech = Tecnico::where('id',$solicitud->technicianId)->first();
        $user = User::where('id',$userClient->userId)->first();
        $nameActividad = Tipo_Actividad::where('id',$solicitud->activityId)->value('description');
        $data = [
            'typeNotification' => $config['type'],
            /**------------------------------------------- */
            'full_name' => $userTech->firstName . ' '. $userTech->lastName ,//Tecnico::where('id',$solicitud->technicianId)->select(DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\') ) AS full_name '))->first(),
            'rate' => $userTech->average_rating,
            'photo'=> $userTech->photo,
            'id_technician' => $userTech['id'],
            'id_client' => $userClient['id'],
            'actividad' => $nameActividad ,
            'description' => $solicitud->requestDescription,
            'ubicacion' => 'lat:' . $solicitud->latitude . ' ' . 'lng:' . $solicitud->longitude,
            'referencia_ubicacion' => $solicitud->serviceLocation,
            'estado_del_servicio' => $config['type'],
            'id_request' => $solicitud->id,
            'date_request' => $solicitud->registrationDateTime,
        ];
        //dd($data);
        $notification = new Notification();
        //dd($notification);
            $notification->action_key = $actionKey;
            $notification->title = $config['title'];
            $notification->body = $config['body'];
            $notification->data = $data;
            $notification->type = 1;
            $notification->type_users = $user->type_user;
            $notification->send_at = Carbon::now();
            $notification->status = 2;
            $notification->sender_id = $user->id;
        $notification->save();

        $data['id'] = $notification->id;
        $notification->data = $data;
        $notification->save();
        $userRecept = Tecnico::find($solicitud->technicianId);
        $userReceive = User::where('id',$userRecept->userId)->first();

        $device = DevicesUser::where('users_id',$userRecept->userId)->first();
        $deviceUser = Devices::where('id',$device->device_id)->first();

        $notificationsUsers = new NotificationUser();
            $notificationsUsers->created_at = Carbon::now();
            $notificationsUsers->expo_response = null ;
            $notificationsUsers->user_id = $user->id;
            $notificationsUsers->type_users = $user->type_user;
            $notificationsUsers->notification_id = $notification->id;
        $notificationsUsers->save();

        SendNotificationJob::dispatch($notification->id,$user->id);
       }
}
