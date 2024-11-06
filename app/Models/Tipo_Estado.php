<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_Estado extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    protected $table = 'state_types';  // Traducción de 'tipo_estados'

    protected $fillable = [
        'description',  // Traducción de 'descripcion'
        'entity_type'
    ];

    public function contact(){
        return $this->belongsTo(Tipo_Estado::class,'statusId');
    }
    public function state(){
        return $this->belongsTo(Servicio::class,'stateId');
    }
}
