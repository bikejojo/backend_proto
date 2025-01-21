<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    //
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table ='type';
    protected $fillable = [
        'description',
        'code_notifications'
    ];
}
