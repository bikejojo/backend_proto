<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Detalle_Agenda_Tecnico extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'detail_technical_agenda';

    protected $fillable = [
        'clientId',
        'typeClient',
        'typeJob',
        'serviceId',
        'citationId',
        'agendaTechnicalId',
        'createDate',
        'serviceDate',
        'citationDate'
    ];

    public function details(){
        return $this->belongsTo(Agenda_Tecnico::class,'agendaTechnicalId');
    }

    public function citations(){
        return $this->hasMany(Cita::class,'citationId');
    }
    public function services(){
        return $this->hasMany(Servicio::class,'serviceId');
    }
}
