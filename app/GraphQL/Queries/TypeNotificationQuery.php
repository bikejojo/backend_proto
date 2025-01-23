<?php

namespace App\GraphQL\Queries;

use App\Models\Type;

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
}
