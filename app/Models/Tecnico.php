<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'tecnicos';
    protected $fillable = [
        'nombre',
        'apellido',
        'carnet-anverso',
        'carnet-reverso',
        'correo',
        'contrasenia',
        'foto',
        'users_id'

    ];

    public function tecnicos() {
        return $this->belongsTo(User::class,'users_id');
    }
}
