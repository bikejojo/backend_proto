<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    //
    protected $id='id';
    protected $table='promotion';
    protected $fillable=[
        'codePromotion',
        'namePromotion',
        'description',
        'type',
        'discount_value',
        'createDate',
        'finishDate',
        'duration',
        'status',
    ];
}
