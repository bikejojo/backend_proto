<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda_Tecnico extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $tables = 'agenda_tecnicos';
    protected $fillable = [
        'tecnico_id',
        'cliente_id',
        'note_id',
        'cita_id',
        'fecha_creada',
    ];

    public function tecnicos(){
        return $this->hasMany(Tecnico::class,'tecnico_id');
    }
    public function clientes(){
        return $this->HasMany(Cliente_Externo::class,'cliente_id');
    }
    public function notes(){
        return $this->hasMany(Note::class,'note_id');
    }
    public function citas(){
        return $this->hasMany(Cita::class,'cita_id');
    }

    public function agenda_tecnicos(){
        return $this->belongsTo(Agenda_Tecnico::class,'agenda_tecnico_id');
    }
}
