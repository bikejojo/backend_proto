<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publicidad extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'publicity';

    protected $fillable = [
        'descriptionPublicity',
        'logo',
        'commercialName',
        'link',
        'createdDate',
        'startDate',
        'finishDate',
        'status',
        'categoryId'
    ];

    public function category(){
        return $this->belongsTo(categoria_publicidad::class,'categoryId');
    }
}
