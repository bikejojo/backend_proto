<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo_Actividad extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'activity_types';
    protected $fillable =[
        'descripcion',
        'entity_type'
    ];


    public function activity(){
        return $this->belongsTo(Cita::class,'activityId');
    }
}
