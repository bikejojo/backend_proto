<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'settings';
    protected $fillable = [
        'sopport_number',
        'screens',
        'dateRegistered',
    ];

}
