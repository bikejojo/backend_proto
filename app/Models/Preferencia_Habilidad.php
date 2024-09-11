<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preferencia_Habilidad extends Model
{
    use HasFactory;
    protected $table="preferencia_habilidades";
    protected $PrimaryKey="id";
    protected $fillable = [
        'habilidades_id',
        'cliente_id',
    ];

    public function habilidades(){
        return $this->belongsTo(Habilidad::class,'habilidades_id');
    }

    public function cliente_externos(){
        return $this->belongsTo(Cliente_Externo::class,'cliente_id');
    }
}

