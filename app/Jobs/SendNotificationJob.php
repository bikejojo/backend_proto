<?php

namespace App\Jobs;

use App\Models\NotificationUser;
use App\Models\NotificationsDevice;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Support\Facades\Log;

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

            $senderId = is_array($this->data['sender_id'])
                ? $this->data['sender_id'][0]
                : $this->data['sender_id'];

            // Validar y procesar receiver_userid
            $receiverIds = is_array($this->data['recipient_id']) ? $this->data['recipient_id'] : [$this->data['recipient_id']];
            // Guardar cada combinación de sender y receiver

            foreach ($receiverIds as $receiverId) {
                NotificationUser::create([
                    'notifications_id' => $this->notification->id,
                    'datetime' => Carbon::now(),
                    'recipient_id' => $receiverId, // Guardar correctamente el receiver actual
                    'sender_id' => $senderId,    // Guardar correctamente el sender
                    'recipient_type' => $this->data->recipient_type,
                    'is_read' => false,
                ]);

                NotificationsDevice::create([
                    'device_id',
                    'token',
                    'is_active',
                    'date',
                    'tokenable_type',
                    'tokenable_id',
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
