<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeviceTokenMutations
{
    public function register($root , array $args){
        $deviceData = $args['deviceTokenRequest'];
        /*$user = Auth::user();
        if(!$user){
            return [
                'message' => 'Usuario no autenticado',
                'success' => false
            ];
        }*/

        //$token = DeviceToken::generateUniqueToken();
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
            return [
                'message' => 'No existe.'
            ];
        }

        return [
            'token' => $deviceToken->token,
            'message' => 'Token generado correctamente.',
            'success' => true,
        ];
    }
}
