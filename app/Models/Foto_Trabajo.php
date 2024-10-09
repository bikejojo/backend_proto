<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto_Trabajo extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'work_photos';

    protected $fillable = [
        'description',  // descripcion
        'photoUrls'  // fotos_url
    ];

    // Relación con Technician (Tecnico)
    public function technician()
    {
        return $this->belongsTo(Tecnico::class, 'technicianId');
    }
}
