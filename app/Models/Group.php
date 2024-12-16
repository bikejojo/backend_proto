<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    protected $PrimaryKey = 'id';
    protected $table= 'group';
    protected $fillable = [
        'name'
    ];

    public function skillGroup(){
        return $this->hasMany(Skills_group::class,'groupId','id');
    }
}
