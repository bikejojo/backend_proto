<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion_suscripcion extends Model
{
    //
    protected $PrimaryKey='id';
    protected $table='promotion_suscription';
    protected $fillable=[
        'subcriptionsId',
        'promotionId',
    ];
}
