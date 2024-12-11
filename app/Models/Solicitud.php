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
        'stateId',
        'clientId',
        'technicianId',
        'activityId',
        'titleRequests',
        'requestDescription',
        'latitude',
        'longitude',
        'reference_phone',
        'status',
        'registrationDateTime',
    ];

    // Relación con Technician (Tecnico)
    public function technician()
    {
        return $this->belongsTo(Tecnico::class, 'technicianId');
    }

    // Relación con ExternalClient (Cliente Externo)
    public function client()
    {
        return $this->belongsTo(Cliente_Interno::class, 'clientId');
    }

    // Relación con StateType (Tipo_Estado)
    public function state()
    {
        return $this->belongsTo(Tipo_Estado::class, 'stateId');
    }
    public function service(){
        return $this->belongsTo(Servicio::class,'requestsId');
    }

}
