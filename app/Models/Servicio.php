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
        'techinicalId',
        'clientId',
        'typeClient',
        'serviceDescription',
        'programDate',
        'requestsDate',
        'finishedDate',
    ];

    public function state(){
        return $this->hasMany(Tipo_Estado::class,'stateId');
    }
    public function resquest(){
        return $this->hasMany(Solicitud::class,'requestsId');
    }
    public function service(){
        return $this->belongsTo(Cita::class,'serviceId');
    }

}
