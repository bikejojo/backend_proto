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
        'name',  // nombre
        'photo',
        'icons',
        'status', // 1 activo, 0 inactivo
    ];

    // Relación con TechnicianSkill (Tecnico_Habilidad)
    public function technicianSkills()
    {
        return $this->hasMany(Tecnico_Habilidad::class, 'skillId');
    }

    public function groupSkill()
    {
        return $this->hasMany(Skills_group::class, 'skillsId', 'id');
    }
}
