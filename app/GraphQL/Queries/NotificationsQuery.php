<?php

namespace App\GraphQL\Queries;

use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\NotificationsDevice;
use App\Models\Tecnico;
use App\Models\User;
use App\Services\ValidationModels;
use Illuminate\Support\Facades\DB;

class NotificationsQuery
{
    /** @param  array{}  $args */

    public function getNotificationsTech($root,array $args){
        try {
            $input = $args['Input'];
            $userId = $input['userTech'];
            $technician=Tecnico::where('id',$userId)->first();
            $user = User::find($technician->userId);

            if(!$user){
                return [
                    'message'=>'No se encontró el usuario para el dispositivo especificado.',
                    'status' => '2'
                ];
            }

            $notification = Notification::select('title','body','data')
                            ->where('sender_id',$user->id);
            $notificacionMandaste = $notification->get();

            $notificationUser = NotificationUser::join('notifications','notifications_user.notification_id','=','notifications.id')
                                ->where('notifications_user.user_id',$user->id)
                                ->select('notifications.title','notifications.body','notifications.data');
            $notificationRecibidad = $notificationUser->get();

            return [
                'message' => 'Notificaciones para el usuario: ' . $technician->firstName .' '. $technician->lastName,
                'notificationSend' => $notificacionMandaste,
                'notificationGet' => $notificationRecibidad,
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
