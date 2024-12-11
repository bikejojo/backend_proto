<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'technicians';

    protected $fillable = [
        'firstName',  // nombre en el esquema GraphQL
        'lastName',   // apellido en el esquema GraphQL
        'frontIdCard',  // carnet_anverso
        'backIdCard',   // carnet_reverso
        'email',
        'phoneNumber',  // telefono
        'password',     // contrasenia
        'average_rating',
        'photo',
        'userId',         // foto
        'cityId'
    ];

    // Relación con User (Usuario)
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Relación con City (Ciudad)
    public function city()
    {
        return $this->belongsTo(Ciudad::class, 'cityId');
    }

    // Relación con TechnicianSkill (Tecnico_Habilidad)
    public function technicianSkills()
    {
        return $this->hasMany(Tecnico_Habilidad::class, 'technicianId');
    }

    // Relación con Request (Solicitud)
    public function requests()
    {
        return $this->hasMany(Solicitud::class, 'technicianId');
    }

    // relacion de tecnico (asociacionclienteTecnico)
    public function associantions(){
        return $this->belongsTo(Asociacion_Cliente_Tecnico::class,'technicalId');
    }

    public function agenda(){
        return $this->belongsTo(Agenda_Tecnico::class,'technicianId');
    }

    public function ratings(){
        return $this->hasMany(Calificacion::class,'technicialId');
    }

    public function averageRating(){
        return $this->ratings()->avg('rating');
    }

    public function totalRarings(){
        return $this->ratings()->count();
    }
}
