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
        'email',
        'telefono',
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
    public function certificaciones(){
        return $this->hasMany(Certificacion::class,'tecnico_id');
    }

    public function tecnicoHabilidades(){
        return $this->hasMany(Tecnico_Habilidad::class,'tecnico_id');
    }

    public function solicitud(){
        return $this->hasMany(Solicitud::class,'tecnico_id');
    }
    public function agendas(){
        return $this->hasMany(Agenda_Tecnico::class,'tecnico_id');
    }
}
