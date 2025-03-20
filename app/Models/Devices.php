<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Devices extends Model
{
    //
    use HasFactory;

    protected $primaryKey='id';
    protected $table='devices';
    protected $fillable =[
        'name_device',
        'type_device',
        'expo_token',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_device');
    }

    // Relación con `create_notifications_device` (Tokens Expo)
    public function notificationTokens()
    {
        return $this->morphMany(DeviceNotifications::class, 'tokenable');
    }
}
