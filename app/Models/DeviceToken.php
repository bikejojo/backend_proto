<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class DeviceToken extends Model
{
    //
    use HasFactory;

    protected $primaryKey='id';
    protected $table='device_token';
    protected $fillable =[
        'name_device',
        'type_device',
        'expo_token',
        'user_id'
    ];


}
