<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeNotification extends Model
{
    //
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'type_notifications';
    protected $fillable = [
        'notifications_id',
        'type_id',
        'title',
        'description',
        'data',
        'read',
        'image',
        'status',
        'read_at',
        'date_time_at',
    ];

    protected $casts = [
        'data' => 'array', // Esto convierte el JSON en un array asociativo automáticamente
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notifications_id');
    }
}
