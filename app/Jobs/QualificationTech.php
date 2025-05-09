<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Notification;
use App\Models\NotificationUser;
use Illuminate\Queue\SerializesModels;
use App\Services\DiccionaryNotifications;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QualificationTech implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationId;
    protected $receptorId;
    private $message;
    /**
     * Create a new job instance.
     */
    public function __construct($notificationId , $receptorId , $message)
    {
        $this->notificationId = $notificationId;
        $this->receptorId = $receptorId;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try{
            if( $this->message === 'services_finish_tech'){
                $notification = Notification::find($this->notificationId);
                Log::info("[JOB] Notificaciones {$notification}");
                if(!$notification){
                    Log::warning("[JOB] Notificación {$this->notificationId} no encontrada.");
                    return [
                        'message' => 'No se encontró la notificación.',
                        'success' => false
                    ];
                }

                $user  = $notification->recipients()->where('user_id',$this->receptorId)->first();
                Log::info("[JOB] Notificaciones {$user}");
                if(!$user){
                    Log::warning("[JOB] Usuario {$this->receptorId} no encontrado para la notificación {$this->notificationId}.");
                    return [
                        'message' => 'No se encontró el usuario receptor.',
                        'success' => false
                    ];
                }
                $devicesUser = DevicesUser::where('users_id',$user->id)->first();
                $devices = Devices::where('id',$devicesUser->device_id)->first();
                $config = DiccionaryNotifications::getByKey('qualification_c');

                $response = Http::post('https://exp.host/--/api/v2/push/send',[
                    'to' => $devices->expo_token,
                    'title' => $config['title'],
                    'body' => $config['body'],
                    'data' => $notification->data,
                ]);

                $NotificationUser = NotificationUser::where('notification_id',$notification->id)->first();
                    $NotificationUser->expo_response = json_encode($response->json());
                    $NotificationUser->user_id = $this->receptorId;
                $NotificationUser->save();

            }else{
                $notification = Notification::find($this->notificationId);
                Log::info("[JOB] Notificaciones {$notification}");
                if(!$notification){
                    Log::warning("[JOB] Notificación {$this->notificationId} no encontrada.");
                    return [
                        'message' => 'No se encontró la notificación.',
                        'success' => false
                    ];
                }

                $user  = $notification->recipients()->where('user_id',$this->receptorId)->first();
                Log::info("[JOB] Notificaciones {$user}");
                if(!$user){
                    Log::warning("[JOB] Usuario {$this->receptorId} no encontrado para la notificación {$this->notificationId}.");
                    return [
                        'message' => 'No se encontró el usuario receptor.',
                        'success' => false
                    ];
                }
                $devicesUser = DevicesUser::where('users_id',$user->id)->first();
                $devices = Devices::where('id',$devicesUser->device_id)->first();
                $config = DiccionaryNotifications::getByKey('qualification_done');

                $response = Http::post('https://exp.host/--/api/v2/push/send',[
                    'to' => $devices->expo_token,
                    'title' => $config['title'],
                    'body' => $config['body'],
                    'data' => $notification->data,
                ]);

                $NotificationUser = NotificationUser::where('notification_id',$notification->id)->first();
                $NotificationUser->expo_response = json_encode($response->json());
                $NotificationUser->user_id = $this->receptorId;
                $NotificationUser->save();
            }
        } catch( \Exception $e ){
            Log::error('Error al enviar la notificación: ' . $e->getMessage());
            return [
                'message' => 'Error al enviar la notificación.' . $e->getMessage(),
                'success' => false
            ];
        }
    }
}
