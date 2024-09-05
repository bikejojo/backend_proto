<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $tables = 'fotos';
    protected $fillable=[ 
        'fotos',
        'trabajo_foto_id',
    ];
 }
