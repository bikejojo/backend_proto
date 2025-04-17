<?php

namespace App\GraphQL\Queries;

use App\Models\Notification;
use App\Models\NotificationUser;
use  App\Models\NotificationsDevice;
use Illuminate\Support\Facades\DB;

class NotificationsQuery
{
    /** @param  array{}  $args */

    public function getNotificationsTech($root,array $args){
        try {
            $input = $args['Input'];
            $userId = $input['userId'];
            $notifications = Notification::where('status', 1)
                ->where('userId', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $notifications->each(function ($notification) {
                $notification->read = NotificationUser::where('notificationId', $notification->id)->exists();
            });

            return [
                'message' => 'Notificaciones obtenidas correctamente.',
                'notifications' => $notifications
            ];
        } catch (\Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' - $e->getMessage(),
                'status' => '3'
            ];
        }
    }
}
