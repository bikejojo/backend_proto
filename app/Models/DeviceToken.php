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
        'tokenable_type',
        'tokenable_id',
        'token',
        'device_id',
        'is_active' => 'boolean',
        'datetime_at' => 'datetime',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'datetime_at' => 'datetime',
    ];

    /*public function user()
    {
        return $this->belongsTo(User::class);
    }*/

    public static function generateUniqueToken()
    {
        do {
            $token = Str::random(60); // Genera un token de 60 caracteres
        } while (self::where('token', $token)->exists()); // Verifica que sea único

        return $token;
    }

    public function tokenable()
    {
        return $this->morphTo();
    }
}
