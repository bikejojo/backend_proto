<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'contact';
    protected $fillable = [
        'clientInternalId',
        'technicalId',
        'statusId',
        'dateRegistered',
        'issue',
    ];

    public function clients(){
        return $this->hasMany(Cliente_Interno::class,'clientInternalId');
    }
    public function technicals(){
        return $this->hasMany(Tecnico::class,'technicalId');
    }
    public function status(){
        return $this->hasMany(Tipo_Estado::class,'statusId');
    }
}
