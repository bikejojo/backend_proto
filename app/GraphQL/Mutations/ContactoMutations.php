<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Setting;
use Carbon\Carbon;

class ContactoMutations{
    // creacion de contacto en la vist de configuracion 
    public function create($root , array $args ){
        $contactData = $args['settingRequest'];

        $setting = new Setting();
            $setting->sopport_number = '(+591) ' . $contactData['support_number'];
            $setting->screens = json_encode($contactData['screens']);
            $setting->dateRegistered = Carbon::now();
            $setting->save();
        return [
            'message' => 'Registro de soporte exitoso!',
            'contact' => $setting

        ];
    }
    public function update($root , array $args ){
    }
    public function delete($root , array $args ){
    }
}
