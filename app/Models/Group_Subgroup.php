<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group_Subgroup extends Model
{
    //
    protected $PrimaryKey = 'id';
    protected $table = 'group_subgroups';
    protected $fillable = [
        'groupId',
        'subGroupId',
        'createDate',
    ];
}
