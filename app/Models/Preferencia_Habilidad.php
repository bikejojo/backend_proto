<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preferencia_Habilidad extends Model
{
    use HasFactory;
    protected $table = 'skill_preferences';  // Traducción de 'preferencia_habilidades'

    protected $primaryKey = 'id';

    protected $fillable = [
        'skillId',  // Traducción de 'habilidades_id'
        'clientId'  // Traducción de 'cliente_id'
    ];

    // Relación con Skill (Habilidad)
    public function skill()
    {
        return $this->belongsTo(Habilidad::class, 'skillId');  // Traducción de 'habilidades'
    }

    // Relación con ExternalClient (Cliente Externo)
    public function externalClient()
    {
        return $this->belongsTo(Cliente_Interno::class, 'clientId');  // Traducción de 'cliente_externos'
    }
}

