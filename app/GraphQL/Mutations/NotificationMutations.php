<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


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
        DB::beginTransaction();
        try{
            $input = $args['input'];
            $notifications = Notification::create([
                'title' => $input['title'],
                'body' => $input['body'],
                'data' => $input['data'] ?? null,
                'type' => (string) $input['type'],
                'datetime_send' => $input['datetime_send'] ?? Carbon::now(),
                'status' => $input['status'],
            ]);
            if(!$notifications){
                DB::rollBack();
                return [
                    'message'=>'Error al crear la notificacion.' ,
                    'success'=>false,
                    'notification'=>null,
                ];
            }

            $dataJob = [
                'sender_id'    => $input['data']['sender_userid'] ?? null,
                'recipient_id' => $input['data']['recipient_userid'] ?? [],
                'expo_token'   => $input['data']['token_user'] ?? null,
                'device'       => $input['data']['device_id'] ?? null,

            ];
            Log::info('[MUTATION] Despachando job para notificación ID: ' . $notifications->id);
            SendNotificationJob::dispatch($notifications,$dataJob['sender_id'],$dataJob);
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
