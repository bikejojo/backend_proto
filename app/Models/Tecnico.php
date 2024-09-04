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
        'carnet_anverso',
        'carnet_reverso',
        'correo',
        'contrasenia',
        'foto',
        'users_id',
        'ciudades_id',
    ];

    public function users() {
        return $this->belongsTo(User::class,'users_id');
    }
    public function ciudades() {
        return $this->belongsTo(Ciudad::class,'ciudades_id');
    }
}
