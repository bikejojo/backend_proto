<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificacion extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'certifications';

    protected $fillable = [
        'name',  // nombre
        'photoUrl',  // foto_url
        'certificationDate'  // fecha_certificacion
    ];

    // Relación con Technician (Tecnico)
    public function technician()
    {
        return $this->belongsTo(Tecnico::class, 'technicianId');
    }
}
