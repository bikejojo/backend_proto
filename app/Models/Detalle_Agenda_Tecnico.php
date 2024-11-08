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
        'serviceId',
        'agendaTechnicalId',
        'createDate',
        'serviceDate',
    ];

    public function details(){
        return $this->belongsTo(Agenda_Tecnico::class,'agendaTechnicalId');
    }

    public function service(){
        return $this->belongsTo(Servicio::class,'serviceId','id');
    }
}
