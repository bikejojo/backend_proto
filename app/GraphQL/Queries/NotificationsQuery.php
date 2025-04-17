<?php

namespace App\GraphQL\Queries;

use App\Models\Notification;
use App\Models\NotificationUser;
use  App\Models\NotificationsDevice;
use App\Services\ValidationModels;
use Illuminate\Support\Facades\DB;

class NotificationsQuery
{
    /** @param  array{}  $args */

    public function getNotificationsTech($root,array $args){
        try {
            $input = $args['Input'];
            $userId = $input['userTech'];

            $user = ValidationModels::validation_user($userId);
            if(!$user){
                return [
                    'message'=>'No se encontró el usuario para el dispositivo especificado.',
                    'status' => '2'
                ];
            }
            //dd($user);
            $notifications = Notification::where('type_users', '1')
                ->where('sender_id', $user->id)
                ->orderBy('send_at', 'desc')
                ->get();
            //dd($notifications);
            $notificationsUser = NotificationUser::where('user_id', $user->id)
                ->where('type_users', '1')
                ->orderBy('created_at', 'desc')
                ->get();
            //dd($notificationsUser);

        } catch (\Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' - $e->getMessage(),
                'status' => '3'
            ];
        }
    }

    public function getNotificationsClient($root,array $args){
        try {
            $input = $args['Input'];
            $userId = $input['userClient'];

            $user = ValidationModels::validation_user($userId);
            if(!$user){
                return [
                    'message'=>'No se encontró el usuario para el dispositivo especificado.',
                    'status' => '2'
                ];
            }
            dd($user);
            $notifications = Notification::where('type_users', '2')
                ->where('sender_id', $user->id)
                ->orderBy('send_at', 'desc')
                ->get();
            //dd($notifications);
            $notificationsUser = NotificationUser::where('user_id', $user->id)
                ->where('type_users', '2')
                ->orderBy('created_at', 'desc')
                ->get();
            //dd($notificationsUser);

        } catch (\Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' - $e->getMessage(),
                'status' => '3'
            ];
        }
    }
}
