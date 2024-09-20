<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial_Servicios extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $tables = 'historial_servicios';
    protected $fillable = [
       'cliente_id',
       'tecnico_id',
       'solicitud_id',
       'agenda_tecnico_id',
       'fecha_realizada',
       'fecha_acabado',
       'descripcion',
       'duracion',
    ];
}
