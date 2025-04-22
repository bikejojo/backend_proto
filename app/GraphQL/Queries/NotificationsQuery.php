<?php

namespace App\GraphQL\Queries;

use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\NotificationsDevice;
use App\Models\Tecnico;
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
            $technician = Tecnico::where('userId' , $user->id)->first();
            if(!$user){
                return [
                    'message'=>'No se encontró el usuario para el dispositivo especificado.',
                    'status' => '2'
                ];
            }
            //dd($user);
            $notifications = Notification::where('type_users', '1')
                ->where('sender_id', $user->id)
                ->select(
                        'notifications.data as data',
                        'notifications.action_key as action_key',
                        'notifications.title as title',
                        'notifications.send_at as send_at',
                        'notifications.sender_id as sender_id',
                    )
                ->orderBy('send_at', 'desc');
                //->get();
            $notificacionSent = $notifications->get()->map(function ($item){
                return [
                    'actions_key' => $item->action_key,
                    'sent_at' => $item->send_at,
                    'title' => $item->title,
                    'data' => $item->data ,//json_decode($item->data),
                    'sender_id' => $item->sender_id,
                ];
            });
            //dd($notifications);
            $notificationsUser = NotificationUser::join('notifications','notifications_user.notification_id','=','notifications.id')
                ->where('notifications_user.user_id', $user->id)
                ->where('notifications_user.type_users', '1')
                ->select(
                    'notifications.data as data',
                    'notifications.action_key as action_key',
                    'notifications.title as title',
                    'notifications.sender_id as sender_id',
                    'notifications_user.expo_response as expo_response',
                    'notifications_user.created_at as created_at',
                    'notifications_user.notification_id as notification_id',)
                ->orderBy('notifications_user.created_at', 'desc');
                //->get();
            //dd($notificationsUser);
            $notificationsReceive = $notificationsUser->get()->map(function ($item){
                return [
                    'actions_key' => $item->action_key,
                    'sent_at' => $item->created_at,
                    'title' => $item->title,
                    'data' => json_decode($item->data, true),
                    'sender_id' => $item->sender_id,

                    'expo_response' => $item->expo_response,
                    'notification_id' => $item->notification_id,
                    'created_at' => $item->created_at,
                ];
            });

            return [
                'message' => 'Las notificaciones de un tecnico',
                'technician' => $technician ,
                'notification' => $notificacionSent,
                'notifications' => $notificationsReceive,
            ];

        } catch (\Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' . $e->getMessage(),
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
            //d($user);
            $notifications = Notification::where('type_users', '2')
                ->where('sender_id', $user->id)
                ->orderBy('send_at', 'desc')
                ->get();
            //dd($notifications);
            $notificationsUser = NotificationUser::where('user_id ', $user->id)
                ->where('type_users', '2')
                ->orderBy('created_at', 'desc')
                ->get();
            //dd($notificationsUser);

        } catch (\Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' . $e->getMessage(),
                'status' => '3'
            ];
        }
    }
}
