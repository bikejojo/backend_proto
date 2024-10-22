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
        'technicianId',
        'clientId',
        'serviceId',
        'activityId',
        'typeClient',
        'citationDescription',
        'cratedDate',
        'nextDate'
    ];

    public function service(){
        return $this->hasMany(Servicio::class,'serviceId');
    }
    public function activity(){
        return $this->hasMany(Tipo_Actividad::class,'activityId');
    }

}
