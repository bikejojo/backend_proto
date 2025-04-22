<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'notifications';
    protected $fillable = [
        'action_key',
        'title',
        'body',
        'data',
        'type',
        'type_users',
        'send_at',
        'status',
        'sender_id',
    ];
    // El usuario que envió/generó la notificación
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Los usuarios que recibirán esta notificación
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'notifications_user')
                    ->withPivot('expo_response', 'is_read')
                    ->withTimestamps();
    }

    protected $casts = [
        'data' => 'array', // Laravel se encarga del encode/decode
    ];

}
