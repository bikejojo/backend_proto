<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Events\SuscriptionChange;
use App\Jobs\RenovationSuscription;
use App\Models\Tecnico;
use App\Services\DiccionaryNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarSuscriptionChange
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(SuscriptionChange $event): void
    {
        $technician = $event->technician;
        $config = DiccionaryNotifications::getByKey('renovation_suscription');
        $user = User::where('id', $technician->userId)->first();

        $fullName = $technician->firstName . ' ' . $technician->lastName;
        $config['body'] = str_replace('{nombre}', $fullName, $config['body']);

        $userDevice = DevicesUser::where('users_id',$user->id)->first();
        //dd($userDevice);
        $devices = Devices::where('id',$userDevice->device_id)->first();

        $notification = new Notification();
            $notification->action_key = 'renovation_suscription';
            $notification->title = $config['title'];
            $notification->body = $config['body'];
            $notification->data = [
                'typeNotification' => $config['type'],
                'id_technician' => $technician->id,
            ];
            $notification->type = 5;
            $notification->status = 2;
            $notification->type_users = $user->type_user;
            $notification->send_at = now();
            $notification->sender_id = $user->id;
        $notification->save();

        $notificattionUser = new NotificationUser();
            $notificattionUser->notification_id = $notification->id;
            $notificattionUser->user_id = $user->id;
            $notificattionUser->type_users = $user->type_user;
            $notificattionUser->created_at = now();
        $notificattionUser->save();

        RenovationSuscription::dispatch($devices,$notification);
    }
}
