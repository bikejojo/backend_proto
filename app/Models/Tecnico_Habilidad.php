<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico_Habilidad extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'technician_skills';

    protected $fillable = [
        'experience',  // experiencia
        'description',  // descripcion
        'technicianId',
        'skillId'
    ];

    // Relación con Technician (Tecnico)
    public function technician()
    {
        return $this->belongsTo(Tecnico::class, 'technicianId');
    }

    // Relación con Skill (Habilidad)
    public function skill()
    {
        return $this->belongsTo(Habilidad::class, 'skillId');
    }
}
