<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial_Servicios extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'service_client_history';
    protected $fillable = [
       'clientId',
       'technicianId',
       'jobId',
       'descriptionJob',
       'stateId',
       'outsetDate',
       'finishDate',
       'description',
    ];
}
