<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;
use App\Services\ValidationModels;
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
        $this->app= env('FULL_URL');
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
                'type' => $input['type'],
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
                'sender_userid'   => $input['data']['sender_userid'] ?? null,
                'receiver_userid' => $input['data']['receiver_userid'] ?? [],
                'token_user'      => $input['data']['token_user'] ?? null,
                'type_device'     => $input['data']['type_device'] ?? null,
                'type_id'         => $input['data']['type_id'] ?? null,
                'title'           => $input['title'],
                'description'     => $input['body'],
                'data'            => $input['data'],
                'image_url'       => $input['image_url'] ?? null,
            ];

            SendNotificationJob::dispatch($notifications,auth()->id,$dataJob);
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
