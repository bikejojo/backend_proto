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
            if(!Devices::where('expo_token',$requestDevice['expo_token'])->exists()){
                $device = new Devices();
                    $device->type_device = $requestDevice['type_device'];
                    $device->name_device = $requestDevice['name_device'];
                    $device->expo_token = $requestDevice['expo_token'];
                $device->save();

                $userId = ValidationModels::validation_user($requestDevice['userId']);

                if(!$userId){
                    $deviceUser =new DevicesUser();
                        $deviceUser->device_id = $device->id;
                        $deviceUser->users_id = null;
                    $deviceUser->save();

                    DB::commit();
                    return [
                        'message'=>'Registro exitoso del equipo en el servidor.',
                        'token'=> $device->expo_token,
                        'success'=>1,
                    ];
                }else{
                    $deviceUser =new DevicesUser();
                        $deviceUser->device_id = $device->id;
                        $deviceUser->users_id = $userId->id;
                    $deviceUser->save();
                    DB::commit();
                    return [
                        'message'=>'Registro exitoso del equipo en el servidor.',
                        'token'=> $device->expo_token,
                        'success'=>1,
                    ];
                }

            }else{
                $deviceExists = Devices::where('expo_token',$requestDevice['expo_token'])->first();
                $deviceExists->expo_token = $requestDevice['expo_token'];
                $deviceExists->save();
                $userId = ValidationModels::validation_user($requestDevice['userId']);
                //dd($userId);
                if($userId){
                    $deviceUser = DevicesUser::where('device_id',$deviceExists->id)->first();
                        $deviceUser->users_id = $userId->id;
                        $deviceUser->save();

                    if(!$deviceUser){
                        DB::rollBack();
                        return [
                            'message' => 'Se produjo un error en el guardar dispositivo y usuario.',
                            'success' => 2
                        ];
                    }
                    DB::commit();
                    return [
                        'message'=>'Actualizacion exitoso del equipo en el servidor.',
                        'token'=> $deviceExists->expo_token,
                        'success'=>1,
                    ];
                }
            }
        } catch (\Exception $e){
            DB::rollBack();
            return [
                'message' => 'El error es el siguiente ' . $e->getMessage(),
                'success'=>3
            ];
        }
    }

}
