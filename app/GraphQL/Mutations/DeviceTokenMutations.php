<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\DevicesUser;
use App\Models\Devices;
use App\Services\ValidationModels;

use Illuminate\Support\Facades\DB;

class DeviceTokenMutations
{
    public function register($root , array $args){
        DB::beginTransaction();
        try {
            $requestDevice = $args['deviceTokenRequest'];
            $device = Devices::updateOrCreate([
                'type_device'=>$requestDevice['type_device'],
                'name_device'=>$requestDevice['name_device'],
                'expo_token'=>$requestDevice['expo_token'],
            ]);

            $userId = ValidationModels::validation_user($requestDevice['userId']);
            //dd($userId);
            if(!$userId){
                DB::rollBack();
                return [
                    'message' => 'Se produjo un error en el usuario',
                    'success' => 2
                ];
            }
            //dd($userId);
            $deviceUser = DevicesUser::updateOrCreate([
                'device_id'=>$device->id,
                'users_id'=> $userId->first()->id,
            ]);

            if(!$deviceUser){
                DB::rollBack();
                return [
                    'message' => 'Se produjo un error en el guardar dispositivo y usuario.',
                    'success' => 2
                ];
            }

            DB::commit();
            return [
                'message'=>'Registro exitoso del equipo en el servidor.',
                'token'=> $device->expo_token,
                'success'=>1,
            ];
        } catch (\Exception $e){
            DB::rollBack();
            return [
                'message' => 'El error es el siguiente ' . $e->getMessage(),
                'success'=>3
            ];
        }
    }

}
