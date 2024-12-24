<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StateReference extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'state_reference';
    protected $fillable = [
        'referenceId',
        'stateId',
        'type',
        //'requestId',
        //'serviceId'.
        'descriptionState',
        'observations',
        'dateCreate',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'referenceId', 'id');
    }

    public function stateType()
    {
        return $this->belongsTo(Tipo_Estado::class, 'stateId', 'id');
    }
}
