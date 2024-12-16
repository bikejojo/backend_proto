<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skills_group extends Model
{
    //
    protected $PrimaryKey = 'id';
    protected $table = 'skillsGroups';
    protected $fillable = [
        'groupId',
        'skillsId',
    ];

    public function skill(){
        return $this->belongsTo(Habilidad::class,'skillsId','id');
    }

    public function group(){
        return $this->belongsTo(Group::class,'groupId','id');
    }
}
