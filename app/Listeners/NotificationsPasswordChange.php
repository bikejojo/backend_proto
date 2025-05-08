<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Notification;
use App\Jobs\PasswordChange;
use App\Events\PasswordChanged;
use App\Models\NotificationUser;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\DiccionaryNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Password;

class NotificationsPasswordChange
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PasswordChanged $event)
    {
        //
        $user = $event->user;
        $config = DiccionaryNotifications::getByKey('technician_password');

        $notification = new Notification();
            $notification->action_key = 'technician_password';
            $notification->title = $config['title'];
            $notification->body = $config['body'];
            $notification->data = [
                'typeNotification' => $config['type'],
                'id_technician' => $user->id,
            ];
            $notification->type = 6;
            $notification->status = 2;
            $notification->type_users = $user->type_user;
            $notification->send_at = Carbon::now();
        $notification->save();

        $notificationUser = new NotificationUser();
            $notificationUser->notification_id = $notification->id;
            $notificationUser->user_id = $user->id;
            $notificationUser->type_users = $user->type_user;
            $notificationUser->created_at = Carbon::now();
        $notificationUser->save();
        $deviceUser = DevicesUser::where('users_id',$user->id)->first();
        $devices = Devices::where('id',$deviceUser->device_id)->first();
        PasswordChange::dispatch($devices,$notification);
    }
}
