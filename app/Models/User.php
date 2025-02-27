<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    protected $fillable = [
        'email',
        'password',
        'ci',
        'type_user',
        'token',
        'status',
        //'code',
    ];
    protected $appends = ['token'];

    public function getTokenAttribute()
    {
        return $this->attributes['token'] ?? null;
    }

    public function clientsExterns(){
        return $this->hasMany(Cliente_Interno::class,'userId');
    }
    public function technicians(){
        return $this->hasMany(Tecnico::class,'userId');
    }

    public function deviceTokens()
    {
        return $this->morphMany(DeviceToken::class, 'tokenable');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

}
