<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technician_subcripcion extends Model
{
    //
    protected $id = 'id';
    protected $table = 'technician_subscription';
    protected $fillable = [
        'technicianId',
        'subcriptionsId',
        'starDate',
        'endDate'
    ];
}
