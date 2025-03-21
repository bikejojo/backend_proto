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
        'title',
        'body',
        'data',
        'type',
        'datetime_send',
        'status'
    ];

    protected $casts = [
        'data' => 'array', // Decodifica automáticamente el JSON en un array
        'datetime_send' => 'datetime'
    ];


    public function recipients()
    {
        return $this->hasMany(NotificationUser::class);
    }
}
