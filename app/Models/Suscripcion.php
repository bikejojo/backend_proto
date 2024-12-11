<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    //
    protected $id='id';
    protected $table = 'subcriptions';
    protected $fillable=[
        'account',
        'status',
        'description',
        'createDate',
        'finishDate',
        'technicianId',
    ];
}
