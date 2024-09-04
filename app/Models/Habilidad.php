<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habilidad extends Model
{
    use HasFactory;
    protected $table="habilidades";
    protected $PramiryKey="id";
    protected $fillable = [
        'nombre',
    ];

    public function prefencias_habilidades(){
        return $this->hasMany(Preferencia_Habilidad::class);
    }
}