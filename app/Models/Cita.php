<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'appointments';

    protected $fillable = [
        'appointmentDescription',  // descripcion_cita
        'latitude',  // latitud
        'longitude',  // longitud
        'locationDescription',  // descripcion_ubicacion
        'result',  // resultado
        'registrationDateTime',  // fecha_hora_registrada
        'endDateTime'  // fecha_hora_fin
    ];

    // Relación con Request (Solicitud)
    public function request()
    {
        return $this->belongsTo(Solicitud::class, 'requestId');
    }

    // Relación con StateType (Tipo_Estado)
    public function state()
    {
        return $this->belongsTo(Tipo_Estado::class, 'stateId');
    }

    // Relación con TechnicianSchedule (Agenda_Tecnico)
    public function schedule()
    {
        return $this->hasMany(Agenda_Tecnico::class, 'appointmentId');
    }
}
