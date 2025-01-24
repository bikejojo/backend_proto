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
        'notifications_id',
        'datetime',
        'sender_userid',
        'receiver_userid',
        'sent_at',
        'read_at',
        'status',

    ];
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notifications_id');
    }
}
