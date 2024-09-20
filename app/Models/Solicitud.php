<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $tables = 'solicituds';
    protected $fillable =[
        'fecha_registrado',
        'fecha_vencimiento',
        'estado_id',
        'descripcion_servicio',
        'tecnico_id',
        'cliente_id',
    ];

    public function tecnicos(){
        return $this->hasMany(Tecnico::class, 'tecnico_id');
    }
    public function clientes(){
        return $this->hasMany(Cliente_Externo::class,'cliente_id');
    }
    public function solicitud_detalle(){
        return $this->belongsTo(Solicitud_Detalle::class,'solicitud_id');
    }
}
