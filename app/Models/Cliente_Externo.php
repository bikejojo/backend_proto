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
        'fullName',      // apellido
        'phoneNumber',
        'status'
    ];

    // asociacionTecnicoClients
  /*   public function associantions(){
        return $this->belongsTo(Asociacion_Cliente_Tecnico::class,'id');
    } */
    public function associantions(){
        return $this->hasMany(Asociacion_Cliente_Tecnico::class,'clientId', 'id');
    }

    public function contacts(){
        return $this->belongsTo(Contacto::class,'clientId');
    }
}
