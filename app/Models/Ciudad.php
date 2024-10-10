<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'cities';  // Traducción de 'ciudades'

    protected $fillable = [
        'name'  // Traducción de 'descripcion'
    ];

    // Relación con ExternalClient (Cliente Externo)
    public function externalClients()
    {
        return $this->hasMany(Cliente_Externo::class, 'cityId');  // Traducción de 'clientes_externos'
    }

    // Relación con Technician (Técnico)
    public function technicians()
    {
        return $this->hasMany(Tecnico::class, 'cityId');  // Traducción de 'tecnicos'
    }
}
