<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationUser extends Model
{
    //
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'notifications_user';
    protected $fillable = [
        'notification_id',
        'user_id',
        'type_users',
        'expo_response',
        'created_at',
        //'is_read',
    ];
    /*protected $casts = [
        'is_read' => 'boolean',
    ];*/
}
