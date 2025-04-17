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
                $device = Devices::updateOrCreate([
                    'type_device'=>$requestDevice['type_device'],
                    'name_device'=>$requestDevice['name_device'],
                    'expo_token'=>$requestDevice['expo_token'],
                ]);

                $userId = ValidationModels::validation_user($requestDevice['userId']);

                if(!$userId){
                    $deviceUser = DevicesUser::updateOrCreate([
                        'device_id'=>$device->id,
                        'users_id'=> null,
                    ]);
                        DB::commit();
                        return [
                            'message'=>'Registro exitoso del equipo en el servidor.',
                            'token'=> $device->expo_token,
                            'success'=>1,
                        ];
                }

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
            }else{
                $deviceExists = Devices::where('expo_token',$requestDevice['expo_token'])->first();
                $deviceExists->expo_token = $requestDevice['expo_token'];
                $deviceExists->save();
                $userId = ValidationModels::validation_user($requestDevice['userId']);
                if($userId){
                    $deviceUser = DevicesUser::updateOrCreate([
                        'device_id'=>$deviceExists->id,
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
