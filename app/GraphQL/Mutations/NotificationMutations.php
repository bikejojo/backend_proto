<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Helpers\ImageHelper;

class NotificationMutations
{
    public function send($root, array $args)
    {
        $notifications = Notification::create([
            'description' => $args['input']['description'],
            'datetime' => now(),
            'status' => 1
        ]);

        $validators = ImageHelper::validateImageNotification($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.',
                'upcomingmessage' => 'Registre su usuario'
            ];
        }

        foreach ($args['input']['receiver_userid'] as $userId){
            SendNotificationJob::dispatch($notifications,$userId,$args['input']);
        }

        return [
            'message' => 'Notificaciones enviadas exitosamente.',
            'success' => true
        ];
    }
}
