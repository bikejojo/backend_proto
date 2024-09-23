<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda_Tecnico extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'agenda_tecnicos';
    protected $fillable = [
        'tecnico_id',
        'cliente_id',
        'note_id',
        'cita_id',
        'fecha_creada',
    ];

    public function tecnicos(){
        return $this->belongsTo(Tecnico::class,'tecnico_id');
    }
    public function clientes(){
        return $this->belongsTo(Cliente_Externo::class,'cliente_id');
    }
    public function notes(){
        return $this->belongsTo(Note::class,'note_id');
    }
    public function citas(){
        return $this->belongsTo(Cita::class,'cita_id');
    }

    public function detalleAgendas(){
        return $this->hasMany(Detalle_Agenda_Tecnico::class,'agenda_tecnico_id');
    }
}
