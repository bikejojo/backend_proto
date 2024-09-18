<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto_Trabajo extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'foto_trabajos';
    protected $fillable = [
        'descripcion',
        'tecnicos_id',
        'fotos_url',
    ];
    protected $casts = [
        'fotos_url' => 'array',
    ];

    public function tecnicos(){
        return $this->belongsTo(Tecnico::class,'tecnico_id');
    }

}
