<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';

    protected $table = 'notes';

    protected $fillable = ['description'];  // Traducción de 'descripcion'

    // Relación con TechnicianSchedule (Agenda_Tecnico)
    public function technicianSchedules()
    {
        return $this->hasMany(Agenda_Tecnico::class, 'noteId');  // Traducción de 'agenda_act'
    }
}
