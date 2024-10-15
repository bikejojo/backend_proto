<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habilidad extends Model
{
    use HasFactory;
    protected $table = 'skills';
    protected $PrimaryKey="id";
    protected $fillable = [
        'name'  // nombre
    ];

    // Relación con TechnicianSkill (Tecnico_Habilidad)
    public function technicianSkills()
    {
        return $this->hasMany(Tecnico_Habilidad::class, 'skillId');
    }

    // Relación con SkillPreference (Preferencia_Habilidad)
    public function skillPreferences()
    {
        return $this->hasMany(Preferencia_Habilidad::class, 'skillId');
    }
}
