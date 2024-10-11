<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente_Externo extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'external_clients';
    protected $fillable = [
        'firstName',      // nombre
        'lastName',       // apellido
        'phoneNumber'
    ];

    // asociacionTecnicoClients
    public function associantions(){
        return $this->belongsTo(Asociacion_Cliente_Tecnico::class,'clientId');
    }
}
