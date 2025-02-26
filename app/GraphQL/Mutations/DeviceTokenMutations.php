<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Service\NotificationService;
use Illuminate\Support\Facades\DB;

class DeviceTokenMutations
{
    public function register($root , array $args){
        DB::beginTransaccion();
        try {
            $deviceData = $args['deviceTokenRequest'];
            $userId = $deviceData['userId'];
            $user = User::find($userId);
            if($user->token){
                $deviceToken = DeviceToken::updateOrCreate([
                    'user_id' => $user->id,
                    'device_type'=>$deviceData['device_type'],
                    'device_name'=>$deviceData['device_name'],
                    'datetime_at' => Carbon::now(),
                    'token' => $user->token
                ]);
            }else{
                DB::rollBack();
                return [
                    'message' => 'No existe el token del usuario.'
                ];
            }
            DB::commit();
            return [
                'token' => $deviceToken->token,
                'message' => 'Token generado correctamente.',
                'success' => true,
            ];
        } catch (\Exception $e){
            DB::rollBack();
            return [
                'message' => 'El error es el siguiente ' . $e->getMessage()
            ];
        }
    }

    public function sendNotification($root,array $args){
        $user = Auth::user();
        if(!$user){
            return false;
        }
        $notificationService = new NotificationService();

        $sent = $notificationService->sendToUser(
            $user,
            'Nueva Notificacion',
            $args['message']
        );

        return $sent;
    }

    public function updateDeviceTokens($root,array $args){
        //$content
        $user = Auth::user();
        if (!$user) {
            return null; // Maneja el caso de usuario no autenticado
        }
        $deviceToken = DeviceToken::updateOrCreate(
            [
                'device_id' => $args['deviceId'],
                'tokenable_id' => $user->id,
                'tokenable_type' => get_class($user),
            ],
            [
                'token' => $args['token'],
                'is_active' => true,
                'datetime_at' => now(),
            ]
        );
        return $deviceToken;
    }
}
