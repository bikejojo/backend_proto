<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico_Habilidad extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'tecnico_habilidades';
    protected $fillable = [
        'tecnico_id',
        'habilidad_id',
    ];
}
