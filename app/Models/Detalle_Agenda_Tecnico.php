<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Detalle_Agenda_Tecnico extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'detalle_agenda_tecnicos';
    protected $fillable = [
        'fecha_proxima',
        'descripcion_proxima',
        'agenda_tecnico_id',
        'tipo_actividad_id',
    ];

    public function agendas(){
        return $this->belongsTo(Agenda_Tecnico::class,'agenda_tecnico_id');
    }

    public function actividades(){
        return $this->belongsTo(Tipo_Actividad::class,'tipo_actividad_id');
    }
}
