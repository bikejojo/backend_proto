<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceNotifications extends Model
{
    //
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'type_notifications';
    protected $fillable = [
        'device_id',
        'token',
        'is_active',
        'date',
        'tokenable_type',
        'tokenable_id',
    ];

    protected $casts = [
        'date' => 'datetime'
    ];

    public function tokenable()
    {
        return $this->morphTo();
    }
}
