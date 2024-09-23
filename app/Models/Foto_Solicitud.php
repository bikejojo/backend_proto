<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto_Solicitud extends Model
{
    use HasFactory;
    protected $table = 'foto_solicituds';
    protected $PrimaryKey = 'id';
    protected $fillable = [
        'descripcion',
        'fotos_url',
        'solicitud_id',
    ];

    public function solicituds(){
        return $this->belongsTo(Solicitud::class,'solicitud_id');
    }
}
