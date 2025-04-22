<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Services\DiccionaryNotifications;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    public function handle(PasswordChanged $event): void
    {
        //
        $user = $event->user;
        $config = DiccionaryNotifications::getByKey('technician_password');
        $notification = new Notification();
            $notification->action_key = 'technician_password';
            $notification->title = $config['title'];
            $notification->body = $config['body'];
            $
        $notification->save();
    }
}
