<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\Models\NotificationsDevice;
use App\Models\NotificationUser;
use App\Models\DevicesUser;
use App\Models\Devices;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\DiccionaryNotifications;

class NotificationMutations
{
    protected $app;
    protected $now;

    public function __construct()
    {
        $this->app= env('APP_URL');
        $this->now = Carbon::now()->format('Ymd_His');
    }
    public function send($root, array $args)
    {
        $input = $args['Input'];
        $actionKey = $input['action_key'];
        $config = DiccionaryNotifications::getByKey($actionKey);

        if(!$config){
            return [
                'message' => 'No se encontró la configuración para la acción especificada.',
                'success' => false
            ];
        }
        $senderDevice = Devices::where('expo_token', $input['senderDevice'])->first();
        $senderUser = DevicesUser::where('device_id', $senderDevice->id)->first();

        if(!$senderUser || !$senderDevice){
            return [
                'message' => 'No se encontró el dispositivo o el usuario para el dispositivo especificado.',
                'success' => false
            ];
        }

        $userIdSender = $senderUser->users_id;

        $userType = User::where('id',$userIdSender)->first();
        $userTypeSender = $userType->type_user;
        //dd($userTypeSender);
        if(!$userIdSender){
            return [
                'message' => 'No se encontró el usuario para el dispositivo especificado.',
                'success' => false
            ];
        }

        DB::beginTransaction();
        try{

            $notification = new Notification();
                $notification->action_key = $config['action_key'] ??  $actionKey;
                $notification->title = $config['title'];
                $notification->body = $config['body'] ?? $input['body'];
                $notification->data = $input['data'] ?? null;
                $notification->type = $config['type'] ?? 'message';
                $notification->send_at = now();
                $notification->status = $config['status'] ?? $input['status'];
                $notification->sender_id = $userIdSender;
                $notification->type_users = $userTypeSender;
            $notification->save();
            foreach ($input['receivers'] as $receiversIds ){
                $receiver = User::find($receiversIds);
                //dd($receiver);
                if(!$receiver) continue;

                $receiversUser = $receiver->id;
                $receiversType = $receiver->type_user;

                $notificationsUsers = new NotificationUser();
                    $notificationsUsers->notification_id = $notification->id;
                    $notificationsUsers->user_id = $receiversUser;
                    $notificationsUsers->type_users = $receiversType;
                    $notificationsUsers->expo_response = $senderDevice->expo_token;
                $notificationsUsers->save();
            }

            SendNotificationJob::dispatch($notification->id);

            DB::commit();
            return [
                'message' => 'Notificaciones enviadas exitosamente.',
                'success' => true
            ];

        } catch(\Exception $e){
            DB::rollBack();
            return [
                'message' => 'Fallas al enviar las notificaciones ' . $e->getMessage()
            ];
        }
    }
}
