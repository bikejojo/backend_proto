<?php

namespace App\Service;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;

class NotificationService {

    private $expoEndpoint = 'https://exp.host/--/api/v2/push/send';

    public function sendToUser($user,$title,$body,$data = []){
        $tokens = $user->deviceTokens()->where('is_active',true)->pluck('token');
        if($tokens->isEmpty()) return false;

        $messages = array_map(function ($token) use ($title, $body, $data) {
            return [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => 'default',
                'priority' => 'high',
            ];
        }, $tokens);

        $response = Http::post($this->expoEndpoint, ['messages' => $messages]);

        if ($response->failed()) {
            return false;
        }

        return true;
    }
}
