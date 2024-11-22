<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lists_Internal_Client extends Model
{
    use HasFactory;
    protected $id = 'id';
    protected $table = 'list_internal_clients';
    protected $fillable = [
        'technicianId',
        'clientId',
        'typeClient',
        'requestsId',
    ];
}
