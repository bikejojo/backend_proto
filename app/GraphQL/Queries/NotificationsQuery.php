<?php

namespace App\GraphQL\Queries;

use App\Models\Notification;
use App\Models\NotificationUser;
use  App\Models\NotificationsDevice;

class NotificationsQuery
{
    /** @param  array{}  $args */

    public function getNotificationsAllYou($root,array $args){
        try {

        } catch (/Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' - $e->getMessage(),
                'status' => ''e
            ];
        }
    }
}
