<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;

class NotificationMutations
{
    public function send($root, array $args)
    {
        $notifications = Notification::create([
            'description' => $args['input']['description'],
            'datetime' => now(),
            'status' => 1
        ]);

        foreach ($args['input']['receiver_userid'] as $userId){
            SendNotificationJob::dispatch($notifications,$userId,$args['input']);
        }

        return [
            'message' => 'Notificaciones enviadas exitosamente.',
            'success' => true
        ];
    }
}
