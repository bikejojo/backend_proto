<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cita extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'citations';

    protected $fillable = [
        'technicialId',
        'clientId',
        'serviceId',
        'activityId',
        'typeClient',
        'citationDescription',
        'cratedDate',
        'nextDate',
        'finishedDate'
    ];

    public function service(){
        return $this->hasMany(Servicio::class,'serviceId');
    }
    public function activity(){
        return $this->hasMany(Tipo_Actividad::class,'activityId');
    }
    public function details(){
        return $this->belongsTo(Detalle_Agenda_Tecnico::class,'citationId');
    }
}
