<?php

namespace App\GraphQL\Mutations;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;
use App\Models\NotificationUser;
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
            $notifications = Notification::create([
                'description' => $args['input']['description'],
                'datetime' => now(),
                'status' => 1
            ]);

            $validators = ImageHelper::validateImageNotification($args);
            if ($validators->fails()) {
                return [
                    'message' => 'Archivo de imagen inválido.',
                    'upcomingmessage' => 'Registre su usuario'
                ];
            }

            $imagePath = null;
            if (isset($args['input']['image']) && $args['input']['image'] instanceof UploadedFile) {
                ImageHelper::createNotifications($notifications->id); // Crear directorio si no existe
                $manager = new ImageManager(new Driver());
                $imagePath = ImageHelper::processImage( $args['input']['image'],
                '/notifications/' . $notifications->id . "/image_notifications_{$this->now}.png",
                $manager);

            }
            //dd($args);

            $imageUrl = env('APP_URL') . '/storage' . str_replace('public/', '', $imagePath);
            foreach ($args['input']['receiver_userid'] as $userIds) {

                $serializedData = [
                    'token_user' => $args['input']['token_user'],
                    'description' => $args['input']['description'],
                    'type_device' => $args['input']['type_device'],
                    'receiver_userid' =>$userIds,
                    'sender_userid' => $args['input']['sender_userid'][0] ?? null,
                    'type_id' => $args['input']['type_id'],
                    'title' => $args['input']['title'],
                    'data' => $args['input']['data'], // Datos adicionales como JSON
                    'image_url' => $imageUrl // Ruta o URL de la imagen
                ];
                //dd($serializedData['receiver_userid']);
                Log::info('Datos enviados al Job:', [
                    'notification' => $notifications,
                    'userId' => $userIds,
                    'input' => $args['input'],
                    'image_url' => $imageUrl
                ]);


                SendNotificationJob::dispatch($notifications, $userIds, $serializedData);
            }

            //dd($serializedData);
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
