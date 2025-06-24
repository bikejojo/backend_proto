<?php

namespace App\Jobs;

use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Notification;
use App\Models\NotificationUser;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $notificationId;
    protected $receptorId;
    protected $userId;
    protected $data;

    public function __construct($notificationId , $receptorId)
    {
        $this->notificationId = $notificationId;
        $this->receptorId = $receptorId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $notification = Notification::find($this->notificationId);
            Log::info("[JOB] Notificaciones {$notification}");
            if(!$notification){
                Log::warning("[JOB] Notificación {$this->notificationId} no encontrada.");
                return [
                    'message' => 'No se encontró la notificación.',
                    'success' => false
                ];
            }

            $user  = $notification->recipients()->where('user_id',$this->receptorId)->get();
            
            if(!$user){
                Log::warning("[JOB] Usuario {$this->receptorId} no encontrado para la notificación {$this->notificationId}.");
                return [
                    'message' => 'No se encontró el usuario receptor.',
                    'success' => false
                ];
            }

            foreach ($user as $users) {
                //Log::info("[JOB] faltas 4" , $users);
                $devicesUser = DevicesUser::where('users_id',$users->id)->first();
                //Log::info("[JOB] Faltas 2 ",  $devicesUser->toArray());
                if (!$devicesUser) continue;
                $devices = Devices::where('id',$devicesUser->device_id)->first();
                //Log::info("[JOB] Faltas 3",  $devices->toArray());
                 if (!$devices) continue;
                $response = Http::post('https://exp.host/--/api/v2/push/send',[
                    'to' => $devices->expo_token,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'data' => $notification->data,
                ]);


                NotificationUser::where('notification_id', $notification->id)
                ->where('user_id', $users->id)
                ->update([
                    'expo_response' => json_encode($response->json()),
                ]);

                Log::info("Notificación enviada a usuario {$this->userId}");
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar la notificación: ' . $e->getMessage());
            return [
                'message' => 'Error al enviar la notificación.' . $e->getMessage(),
                'success' => false
            ];
        }
    }
}
