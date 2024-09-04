<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificacion extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'certificaciones';
    protected $fillable = [
        'nombre',
        'fecha_certificacion',
        'tecnico_id',

    ];
}