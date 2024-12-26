<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubGroup_skill extends Model
{
    //
    protected $PrimaryKey = 'id';
    protected $table = 'sub_groups_skill';
    protected $fillable =[
        'groupId',
        'subGroupId',
        'createDate',
    ];
}
