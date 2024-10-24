<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asociacion_Cliente_Tecnico extends Model
{
    protected $PrimaryKey = 'id';
    protected $table = 'associationTechnClient';
    protected $fillable = [
        'clientId',      // nombre
        'typeClient',
        'citationId',
        'agendaTechnicalId',
        'serviceId',
        'createDate'
    ];

    //tecnicos
    public function technicals(){
        return $this->hasMany(Tecnico::class,'technicalId');
    }
    //cliente externos
    public function clients(){
        return $this->hasMany(Cliente_Externo::class,'clientId');
    }
}
