<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servicio extends Model
{
    //v
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'services' ;
    protected $fillable = [
        'stateId',
        'requestsId',
        'technicalId',
        'activityId',
        'clientId',
        'typeClient',
        'titleService',
        'serviceDescription',
        'serviceLocation',
        'longitude',
        'latitude',
        'createdDateTime',
        'updatedDateTime',
        'finishDateTime_client',
        'finishDateTime_technician',
        'status'
    ];

    public function state(){
        return $this->belongsTo(Tipo_Estado::class,'stateId','id');
    }
    public function resquest(){
        return $this->hasMany(Solicitud::class,'requestsId');
    }
    public function service(){
        return $this->belongsTo(Cita::class,'serviceId');
    }

    public function details(){
        return $this->belongsTo(Detalle_Agenda_Tecnico::class,'serviceId');
    }

    public function rating(){
        return $this->hasOne(Calificacion::class , 'serviceId');
    }
}
