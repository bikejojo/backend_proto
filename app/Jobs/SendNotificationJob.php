<?php

namespace App\Jobs;

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
    protected $userId;
    protected $data;

    public function __construct($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        //dd($this->notification);
        try {
            $notification = Notification::find($this->notificationId);

            if (!$notification) {
                Log::warning("Notificación ID {$this->notificationId} no encontrada.");
                return[
                    'message' => 'Notificación no encontrada.' . $this->notificationId,
                    'success' => false
                ];
            }

            $users = $notification->recipients;

            foreach ($users as $user) {
                $device = $user->devices()->where('is_active', true)->first();
                try {
                    $response = Http::post('https://exp.host/--/api/v2/push/send', [
                        'to' => $device->expo_token,
                        'title' => $notification->title,
                        'body' => $notification->body,
                        'data' => $notification->data ?? [],
                    ]);
                    $json = $response->json();
                    NotificationUser::where('notification_id', $notification->id)
                                ->where('user_id', $user->id)
                                ->update([
                                    'expo_response' => json_encode($json),
                                ]);

                }catch(\Exception $e){
                    Log::error('Error al enviar la notificación: ' . $e->getMessage());
                    return[
                        'message' => 'Error al enviar la notificación: ' . $e->getMessage(),
                        'success' => false
                    ];
                }
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
