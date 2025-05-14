<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Setting;
use App\Models\Tecnico;
use App\Models\Tipo_Actividad;

class SettingQuery
{
    public function list($root,array $args){
        $setting = Setting::all();
        return [
            'message' => 'Listado de soporte',
            'contact' => $setting
        ];
    }

    public function getActivity($root,array $args){
        return Tipo_Actividad::where('status',1)->get();
    }
}
