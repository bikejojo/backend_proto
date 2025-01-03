<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class codeService {

    public static function generatorCodeUser(){
        $res="";

        do {
            $letras=Str::upper(Str::random(4));
            $num=mt_rand(1000,9999);
            $res = $letras.$num;

            $existe = User::where('code',$res)->exists();
        }while($existe);

        return $res;
    }
}
