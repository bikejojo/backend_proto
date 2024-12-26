<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Setting;
use App\Models\Tecnico;

class SettingQuery
{


    public function list($root,array $args){
        $setting = Setting::all();
        return [
            'message' => 'Listado de soporte',
            'contact' => $setting
        ];
    }
}
