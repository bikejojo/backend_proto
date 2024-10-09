<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud_Detalle extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'request_details';  // Traducción de 'solicitud_detalles'

    protected $fillable = [
        'requestId',  // Traducción de 'solicitud_id'
        'requestedSkills'  // Traducción de 'habilidades_solicitadas'
    ];

    // Relación con Request (Solicitud)
    public function request()
    {
        return $this->belongsTo(Solicitud::class, 'requestId');  // Traducción de 'solicitudes'
    }
}
