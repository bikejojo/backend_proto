<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class categoria_publicidad extends Model
{
    //
    use HasFactory;
    protected $PrimaryKey = 'id';
    protected $table = 'category_publicity';

    protected $fillable = [
        'description'
    ];

    public function categorias(){
        return $this->hasMany(Publicidad::class,'categoryId');
    }
}
