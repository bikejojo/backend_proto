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
        'token_user',
        'type_device',
        'notificacions_id',
        'datetime',
        'sender_userid',
        'receiver_usrid',
        'sent_ad',
        'read_id',
        'status',

    ];
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notifications_id');
    }
}
