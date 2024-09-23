<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'solicituds';
    protected $fillable =[
        'fecha_tiempo_registrado',
        'fecha_tiempo_vencimiento',
    
        'descripcion_servicio',
        'tecnico_id',
        'cliente_id',
        'estado_id',
    ];

    public function tecnicos(){
        return $this->belongsTo(Tecnico::class, 'tecnico_id');
    }
    public function clientes(){
        return $this->belongsTo(Cliente_Externo::class,'cliente_id');
    }
    public function solicitud_detalles(){
        return $this->hasMany(Solicitud_Detalle::class,'solicitud_id');
    }
    public function solicituds(){
        return $this->hasMany(Foto_Solicitud::class,'solictud_id');
    }
    public function estados(){
        return $this->belongsTo(Tipo_Estado::class,'estado_id');
    }

}
