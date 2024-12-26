<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StateReference extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'state_reference';
    protected $fillable = [
        //'referenceId',
        'stateId',
        'type',
        'requestId',
        'serviceId',
        'technicianId',
        'clientId',
        'typeClient',
        'descriptionState',
        'observations',
        'dateCreate',
    ];


    public function stateType()
    {
        return $this->belongsTo(Tipo_Estado::class, 'stateId', 'id');
    }

    public function services(){
        return $this->belongsTo(Servicio::class,'serviceId','id');
    }
    public function request(){
        return $this->belongsTo(Solicitud::class,'requestId','id');
    }
}
