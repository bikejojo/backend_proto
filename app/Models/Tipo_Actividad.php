<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_Actividad extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'tipo_actividades';
    protected $fillable =[
        'descripcion'
    ];
}
