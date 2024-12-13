<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Setting;
use App\Models\Tecnico;

class SettingQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function list($root,array $args){
        $setting = Setting::all();
        return [
            'message' => 'Listado de soporte',
            'contact' => $setting
        ];
    }
}
