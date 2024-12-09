<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    //
    protected $id='id';
    protected $table = 'subscriptions';
    protected $fillable=[
        'account',
        'description',
        'createDate',
        'finishDate',
        'technicianId',
    ];
}
