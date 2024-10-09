<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'requests';

    protected $fillable = [
        'registrationDateTime',  // fecha_tiempo_registrado
        'expirationDateTime',  // fecha_tiempo_vencimiento
        'updatedDateTime',  // fecha_tiempo_actualizado
        'requestDescription',  // descripcion_solicitud
        'latitude',  // latitud
        'longitude',  // longitud
        'locationDescription'  // descripcion_ubicacion
    ];

    // Relación con Technician (Tecnico)
    public function technician()
    {
        return $this->belongsTo(Tecnico::class, 'technicianId');
    }

    // Relación con ExternalClient (Cliente Externo)
    public function client()
    {
        return $this->belongsTo(Cliente_Externo::class, 'clientId');
    }

    // Relación con StateType (Tipo_Estado)
    public function state()
    {
        return $this->belongsTo(Tipo_Estado::class, 'stateId');
    }

    // Relación con RequestDetail (Solicitud_Detalle)
    public function requestDetails()
    {
        return $this->hasMany(Solicitud_Detalle::class, 'requestId');
    }

    // Relación con RequestPhoto (Foto_Solicitud)
    public function requestPhotos()
    {
        return $this->hasMany(Foto_Solicitud::class, 'requestId');
    }
}
