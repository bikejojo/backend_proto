<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    //
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'notifications';
    protected $fillable = [
        'datetime',
        'description',
        'status',

    ];
    public function users()
    {
        return $this->hasMany(NotificationUser::class, 'notifications_id');
    }

    public function types()
    {
        return $this->hasMany(TypeNotification::class, 'notifications_id');
    }
}
