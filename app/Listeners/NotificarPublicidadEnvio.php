<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Events\PublicidadEnvio;
use App\Jobs\PublicidadEnvioTech;
use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\DiccionaryNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;


class NotificarPublicidadEnvio
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
    public function handle(PublicidadEnvio $event)
    {
        //dd($event);
        $publicidad = $event->getPublicidad();
        $techIds   = $event->getTechsIds();
        $admin      = $event->getAdmin();

        $actionKey = "send_publicity";
        $config = DiccionaryNotifications::getByKey($actionKey);

        foreach ( $techIds as $techId ) {
            $user = User::where('id',$techId->userId)->first();
            //---------------------------------------------------------------
            $deviceUser = DevicesUser::where('users_id',$user->id)->first();
            $device = Devices::where('id',$deviceUser->device_id)->first();
            //---------------------------------------------------------------
            $notification = new Notification();
                $notification->action_key =  $actionKey;
                $notification->title = $config['title'];
                $notification->body = $publicidad->descriptionPublicity;
                $notification->data = [
                    'descriptionPublicity' =>  $publicidad->descriptionPublicity,
                    'commercialName'       =>  $publicidad->commercialName,
                    'user_id'              =>  $admin->id
                ];
                $notification->type = $config['type'];
                $notification->type_users = $admin->type_user;
                $notification->send_at = Carbon::now();
                $notification->status = $config['status'];
                $notification->sender_id = $admin->id;
            $notification->save();

            $notificationUser = new NotificationUser();
                $notificationUser->notification_id = $notification->id;
                $notificationUser->user_id = $user->id;
                $notificationUser->type_users = $user->type_user;
                $notificationUser->created_at = Carbon::now();
                $notificationUser->is_service_2hr = false;
                $notificationUser->is_service_1hr = false;
                $notificationUser->is_record_subcription = false;
            $notificationUser->save();
            Log::info("[LOG] Ningun error en el envio de publicidad.");
            //---------------------------------------------------------------
            PublicidadEnvioTech::dispatch($notification,$notificationUser,$device);
        }
    }
}
