<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Detalle_Agenda_Tecnico extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'detail_technicial_agenda';

    protected $fillable = [
        'clientId',
        'typeClient',
        'agendaTechnicalId',
        'createDate',
    ];

}
