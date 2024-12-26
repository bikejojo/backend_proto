<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technician_subcripcion extends Model
{
    //
    protected $id = 'id';
    protected $table = 'technician_subcription';
    protected $fillable = [
        'technicianId',
        'subcriptionsId',
        'starDateSubcription',
        'endDateSubcription',
        'status',
    ];

    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'subcriptionsId', 'id');
    }
}
