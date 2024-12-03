<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calificacion extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'rating';

    protected $fillable = [
        'technicialId',
        'serviceId',
        'clientId',
        'rating',
        'feedback',
    ];

    public function technician(){
        return $this->belongsTo(Tecnico::class,'technicialId');
    }

    public function service(){
        return $this->belongsTo(Servicio::class , 'serviceId');
    }

    public function client(){
        return $this->belongsTo(Cliente_Interno::class,'clientId');
    }
}
