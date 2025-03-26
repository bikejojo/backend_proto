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
        'recipient_id',
        'recipient_type',
        'sender_id',
        'sender_type',
        'is_read',
    ];
    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
