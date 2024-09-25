<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'citas';
    protected $fillable = [
        'descripcion_solicitud',
        'latitud',
        'longitud',
        'descripcion_ubicacion',
        'resultado',
        'estado_id',
        'solicitud_id',
        'fecha_registrada',
        'fecha_fin',
        'duracion',
    ];

    public function solicitudes(){
        return $this->belongsTo(Solicitud::class,'solicitud_id');
    }
    public function estados(){
        return $this->belongsTo(Tipo_Estado::class,'estado_id');
    }
    public function agendas(){
        return $this->belongsTo(Agenda_Tecnico::class,'cita_id');
    }
}
