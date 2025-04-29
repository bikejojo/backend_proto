<?php

namespace App\GraphQL\Queries;

use App\Models\Type;
use App\Models\TypeNotification;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Services\ValidationModels;
use Illuminate\Support\Facades\DB;

class TypeNotificationQuery
{
    public function getTypeNotifications($root,array $args){
        $type = Type::whereNotIn('id',[1,2])->get();
       // dd($type);
        return [
            'message' => 'Los tipos de notificaciones',
            'type' => $type
        ];
    }

    public function getNotifications($root,array $args){
        $notificationData=$args['requestNotification'];
        $technicianId=$notificationData['technician_id'];
        $technician=ValidationModels::validationTechnician($technicianId);
        $notificationId = NotificationUser::where('sender_userid',$technician->id)
        ->where('type_id',4)
        ->join('type_notifications','notifications_user.notifications_id','=','type_notifications.notifications_id')
        ->join('type','type_notifications.type_id','=','type.id')
        ->join('internal_clients','notifications_user.receiver_userid','=','internal_clients.id')
        ->select(
            'notifications_user.sent_at',
            'type_notifications.title',
            'type_notifications.description',
            'type_notifications.data',
            'type.code_notifications',
            DB::raw('internal_clients."firstName" || \' \' || internal_clients."lastName" as full_name'), // Concatenar nombres
            'internal_clients.phoneNumber',
            )
        ->get();
      
        return[
            'message'=>'Las notificaciones de tecnico.',
            'technician' => $technician ,
            'notification' => $notificationId
        ];
    }
    public function getNotificationsPublic($root,array $args){
        $notificationData=$args['requestNotification'];
        $technicianId=$notificationData['technician_id'];
        $technician=ValidationModels::validationTechnician($technicianId);
        $notificationId = NotificationUser::where('sender_userid',$technician->id)
        ->where('type_id',3)
        ->join('type_notifications','notifications_user.notifications_id','=','type_notifications.notifications_id')
        ->join('type','type_notifications.type_id','=','type.id')
        ->join('internal_clients','notifications_user.receiver_userid','=','internal_clients.id')
        ->select(
            'notifications_user.sent_at',
            'type_notifications.title',
            'type_notifications.description',
            'type_notifications.data',
            'type.code_notifications',
            DB::raw('internal_clients."firstName" || \' \' || internal_clients."lastName" as full_name'), // Concatenar nombres
            'internal_clients.phoneNumber',
            )
        ->get();
        //dd($notificationId);
        return[
            'message'=>'Las notificaciones de tecnico.',
            'technician' => $technician ,
            'notification' => $notificationId
        ];
    }
}
