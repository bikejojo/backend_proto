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

    // Relación con Certification (Certificación)
    public function certifications()
    {
        return $this->hasMany(Certificacion::class, 'technicianId');
    }

    // Relación con WorkPhoto (Foto_Trabajo)
    public function workPhotos()
    {
        return $this->hasMany(Foto_Trabajo::class, 'technicianId');
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

    // Relación con TechnicianSchedule (Agenda_Tecnico)
    public function schedules()
    {
        return $this->hasMany(Agenda_Tecnico::class, 'technicianId');
    }
}
