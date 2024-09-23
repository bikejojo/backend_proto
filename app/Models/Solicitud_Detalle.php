<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud_Detalle extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'solicitud_detalles';
    protected $fillable = [
        'solicitud_id',
        'habilidades_solicitadas',
    ];

    public function solicitudes(){
        return $this->belongsTo(Solicitud::class,'solicitud_id');
    }
}
