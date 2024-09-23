<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'notes';
    protected $fillable = ['descripcion'];

    public function agenda_act(){
        return $this->hasMany(Agenda_Tecnico::class,'note_id');
    }
}
