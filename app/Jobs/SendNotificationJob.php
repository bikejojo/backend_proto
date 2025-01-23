<?php

namespace App\Jobs;

use App\Models\NotificationUser;
use App\Models\TypeNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Helpers\ImageHelper;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $notification;
    protected $userId;
    protected $data;
    protected $url;

    public function __construct($notification,$userId,$data)
    {
        $this->notification = $notification;
        $this->userId = $userId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
{
    try {

        // Crear la notificación del usuario
        NotificationUser::create([
            'notifications_id' => $this->notification->id,
            'token_user' => is_array($this->data['token_user']) ? json_encode($this->data['token_user']) : ($this->data['token_user'] ?? null),
            'type_device' => is_array($this->data['type_device']) ? json_encode($this->data['type_device']) : ($this->data['type_device'] ?? null),
            'datetime' => Carbon::now(),
            'receiver_userId' => $this->userId,
            'sender_userid' => is_array($this->data['sender_userid']) ? json_encode($this->data['sender_userid']) : ($this->data['sender_userid'] ?? null),
            'sent_at' => now(),
            'read_at' => null
        ]);

        // Crear el tipo de notificación
        $type = TypeNotification::create([
            'notifications_id' => $this->notification->id,
            'type_id' => $this->data['type_id'],
            'title' => $this->data['title'],
            'description' => $this->data['description'],
            'data' => $this->data['data'], // Almacenar JSON
            'image' => $this->url,
            'read' => 0,
            'status' => 1,
            'read_at' => null,
            'date_time_at' => Carbon::now(),
        ]);

        } catch (\Exception $e) {
            Log::error('------------------------------------Error en SendNotificationJob: ' . $e->getMessage(), [
                'notification_id' => $this->notification->id,
                'user_id' => $this->userId,
            ]);
        }
    }

}
