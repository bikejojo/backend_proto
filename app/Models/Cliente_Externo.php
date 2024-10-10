<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente_Externo extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'external_clients';
    protected $fillable = [
        'firstName',      // nombre
        'lastName',       // apellido
        'email',
        'loginMethod',    // metodo_login
        'photo',
        'userId',
        'cityId',          // foto
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

    // Relación con SkillPreference (Preferencia_Habilidad)
    public function skillPreferences()
    {
        return $this->hasMany(Preferencia_Habilidad::class, 'clientId');
    }

    // Relación con Request (Solicitud)
    public function requests()
    {
        return $this->hasMany(Solicitud::class, 'clientId');
    }

    // Relación con TechnicianSchedule (Agenda_Tecnico)
    public function schedules()
    {
        return $this->hasMany(Agenda_Tecnico::class, 'clientId');
    }
}
