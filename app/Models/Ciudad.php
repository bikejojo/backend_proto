<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'ciudades';
    protected $fillable = [
        'descripcion',
    ];

    public function clientes_externos(){
        return $this->hasMany(Cliente_Externo::class,'ciudades_id');
    }
    public function tecnicos(){
        return $this->hasMany(Tecnico::class,'ciudades_id');
    }
}
