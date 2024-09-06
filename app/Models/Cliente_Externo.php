<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente_Externo extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'cliente_externos';
    protected $fillable = [
        'nombre',
        'email',
        'metodo_login',
        'foto',
        'users_id',
        'ciudades_id',
    ];
    public function users() {
        return $this->belongsTo(User::class,'users_id');
    }
    public function ciudades() {
        return $this->belongsTo(Ciudad::class,'ciudades_id');
    }
    public function preferencia_habilidades(){
        return $this->hasMany(Preferencia_Habilidad::class,'cliente_id');
    }
}
