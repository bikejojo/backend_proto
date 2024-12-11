<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    //
    protected $id='id';
    protected $table='payment';
    protected $fillable=[
        'bank',
        'account',
        'social_reason',
        'amount',
        'amount_promotion',
        'amount_pay',
        'method_payment',
        'date_payment',
        'photo_qr',
        'subscriptionId',
        'status',
    ];
}
