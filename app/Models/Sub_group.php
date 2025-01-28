<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sub_group extends Model
{
    //
    protected $PrimaryKey = 'id';
    protected $table = 'sub_groups';
    protected $fillable = [
        'description',
        'createDate',
        'photo',
    ];
}
