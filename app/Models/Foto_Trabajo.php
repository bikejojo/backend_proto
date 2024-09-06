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
        'foto',
        'tecnico_id',
        'url_foto',
    ];

    public function tecnicos(){
        return $this->belongsTo(Tecnico::class,'tecnico_id');
    }

}
