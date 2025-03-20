<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicesUser extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $tables = 'devices_users';
    protected $fillable = [
        'device_id',
        'users_id',
    ];

    public function user(){
        return $this->belongsTo(User::class,'users_id');
    }

    public function device(){
        return $this->belongsTo(Devices::class,'device_id');
    }
}
