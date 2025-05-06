<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\DevicesUser;
use App\Models\Devices;
use App\Services\ValidationModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceTokenMutations
{
    public function register($root , array $args){
        //Log::info('contenido ' , $args);
        $type = $args["deviceTokenRequest"]["type"];
        switch($type){
            case "2":
                DB::beginTransaction();
                try {
                    $requestDevice = $args['deviceTokenRequest'];
                    if(!Devices::where('expo_token',$requestDevice['expo_token'])->exists()){
                        $device = new Devices();
                            $device->type_device = $requestDevice['type_device'];
                            $device->name_device = $requestDevice['name_device'];
                            $device->expo_token = $requestDevice['expo_token'];
                        $device->save();

                        //$userId = ValidationModels::validation_user($requestDevice['userId']);
                        $userTech = ValidationModels::validation_Technician($requestDevice['userId']);
                        $userId = ValidationModels::validation_user($userTech->userId);
                        if(!$userId){
                            $deviceUser =new DevicesUser();
                                $deviceUser->device_id = $device->id;
                                $deviceUser->users_id = null;
                            $deviceUser->save();

                            DB::commit();
                            return [
                                'message'=>'Registro exitoso del equipo invitado en el servidor.',
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
                        Log::info('token si existe en la base da tos ' . $deviceExists);
                        //dd($deviceExists);
                        $deviceExists->expo_token = $requestDevice['expo_token'];
                        $deviceExists->save();
                        Log::info('useeeId'. $requestDevice['userId']);
                        $userTech = ValidationModels::validation_Technician($requestDevice['userId']);
                        $userId = ValidationModels::validation_user($userTech->userId);
                        Log::info('token si existe en la base da tos ' . $userId);
                        if($userId){

                            $deviceUser = new DevicesUser();
                                $deviceUser->users_id = $userId->id;
                                $deviceUser->device_id = $deviceExists->id;
                            $deviceUser->save();
                            Log::info('token si existe en la base da tos ' . $deviceUser);
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
                break;

            case "1":
                DB::beginTransaction();
                try {
                    $requestDevice = $args['deviceTokenRequest'];
                    if(!Devices::where('expo_token',$requestDevice['expo_token'])->exists()){
                        $device = new Devices();
                            $device->type_device = $requestDevice['type_device'];
                            $device->name_device = $requestDevice['name_device'];
                            $device->expo_token = $requestDevice['expo_token'];
                        $device->save();

                        //$userId = ValidationModels::validation_user($requestDevice['userId']);
                        $userTech = ValidationModels::validation_clientInternal($requestDevice['userId']);
                        $userId = ValidationModels::validation_user($userTech->userId);
                        if(!$userId){
                            $deviceUser =new DevicesUser();
                                $deviceUser->device_id = $device->id;
                                $deviceUser->users_id = null;
                            $deviceUser->save();

                            DB::commit();
                            return [
                                'message'=>'Registro exitoso del equipo invitado en el servidor.',
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
                        Log::info('token si existe en la base da tos ' . $deviceExists);
                        //dd($deviceExists);
                        $deviceExists->expo_token = $requestDevice['expo_token'];
                        $deviceExists->save();
                        Log::info('useeeId'. $requestDevice['userId']);
                        $userTech = ValidationModels::validation_clientInternal($requestDevice['userId']);
                        $userId = ValidationModels::validation_user($userTech->userId);
                        Log::info('token si existe en la base da tos ' . $userId);
                        if($userId){

                            $deviceUser = new DevicesUser();
                                $deviceUser->users_id = $userId->id;
                                $deviceUser->device_id = $deviceExists->id;
                            $deviceUser->save();
                            Log::info('token si existe en la base da tos ' . $deviceUser);
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
                break;
        }


    }

}
