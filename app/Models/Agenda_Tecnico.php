<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda_Tecnico extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'technician_schedules';

    protected $fillable = [
        'createdDate',  // fecha_creada
        'nextDate',  // fecha_proxima
        'nextDescription'  // descripcion_proxima
    ];

    // Relación con Technician (Tecnico)
    public function technician()
    {
        return $this->belongsTo(Tecnico::class, 'technicianId');
    }

    // Relación con ExternalClient (Cliente Externo)
    public function client()
    {
        return $this->belongsTo(Cliente_Externo::class, 'clientId');
    }

    // Relación con Appointment (Cita)
    public function appointment()
    {
        return $this->belongsTo(Cita::class, 'appointmentId');
    }

    // Relación con ActivityType (Tipo_Actividad)
    public function activityType()
    {
        return $this->belongsTo(Tipo_Actividad::class, 'activityTypeId');
    }

    // Relación con Note (Nota)
    public function note()
    {
        return $this->belongsTo(Note::class, 'noteId');
    }
}
