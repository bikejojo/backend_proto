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

    public function client(){
        return $this->belongsTo(Cliente_Interno::class,'clientId');
    }

    public function technician(){
        return $this->belongsTo(Tecnico::class,'technicianId');
    }
}
