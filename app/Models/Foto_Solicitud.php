<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto_Solicitud extends Model
{
    use HasFactory;
    protected $table = 'request_photos';  // Traducción de 'foto_solicituds'

    protected $primaryKey = 'id';

    protected $fillable = [
        'description',  // Traducción de 'descripcion'
        'photoUrls',  // Traducción de 'fotos_url'
        'requestId'  // Traducción de 'solicitud_id'
    ];

    // Relación con Request (Solicitud)
    public function request()
    {
        return $this->belongsTo(Solicitud::class, 'requestId');  // Traducción de 'solicituds'
    }
}
