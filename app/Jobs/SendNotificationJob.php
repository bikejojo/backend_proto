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
use App\Models\Type;

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
            Log::info('$this->userId: ' . $this->notification);
            //dd($this->userId);
            // Validar y procesar sender_userid
            $senderId = is_array($this->data['sender_userid']) ? $this->data['sender_userid'][0] : $this->data['sender_userid'];

            // Validar y procesar receiver_userid
            $receiverIds = is_array($this->data['receiver_userid']) ? $this->data['receiver_userid'] : [$this->data['receiver_userid']];
            // Guardar cada combinación de sender y receiver

            foreach ($receiverIds as $receiverId) {
                NotificationUser::create([
                    'notifications_id' => $this->notification->id,
                    'token_user' => $this->data['token_user'] ?? null,
                    'type_device' => $this->data['type_device'] ?? null,
                    'datetime' => Carbon::now(),
                    'receiver_userid' => $receiverId, // Guardar correctamente el receiver actual
                    'sender_userid' => $senderId,    // Guardar correctamente el sender
                    'sent_at' => Carbon::now(),
                    'read_at' => null,
                ]);

                TypeNotification::create([
                    'notifications_id' => $this->notification->id,
                    'type_id' => $this->data['type_id'],
                    'title' => $this->data['title'],
                    'description' => $this->data['description'],
                    'data' => $this->data['data'],
                    'read' => 0,
                    'status' => "1",
                    'read_at' => null,
                    'date_time_at'=>Carbon::now(),
                    'image' => $this->data['image_url']
                ]);
            }
        } catch (\Exception $e) {
            // Registrar el error en los logs
            Log::error('Error en SendNotificationJob: ' . $e->getMessage(), [
                'notification_id' => $this->notification->id,
                'user_id' => $this->userId,
            ]);
        }
    }



}
