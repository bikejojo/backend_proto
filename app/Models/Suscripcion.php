<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    //
    protected $id='id';
    protected $table = 'subscriptions';
    protected $fillable=[
       'payment_date', // Campo para la fecha de pago
        'transaction_code',
        'bank',
        'account',
        'amount', 10, 2,
        'status',
        'escription',
        'photo_qr',
        'userId',
        'typeUser',
        'createDate',
        'finishDate',
    ];
}
