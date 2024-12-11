<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria_Publicidad extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'category_publicity';

    protected $fillable = [
        'description',
        'entity_type',
        'code'
    ];

    public function categories(){
        return $this->hasMany(Publicidad::class,'categoryId');
    }
}
