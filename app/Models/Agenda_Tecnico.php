<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agenda_Tecnico extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'technician_agenda';
    protected $fillable = [
        'technicianId',
        'createDate',
    ];

    public function technician(){
        return $this->belongsTo(Tecnico::class,'technicianId');
    }

    public function details(){
        return $this->hasMany(Detalle_Agenda_Tecnico::class,'agendaTechnicalId');
    }
}
