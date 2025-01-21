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

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $notification;
    protected $userId;
    protected $data;

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
        NotificationUser::create([
            'notifications_id' => $this->notification->id,
            'receiver_userId' => $this->userId,
            'sent_at' => now()
        ]);

        TypeNotification::create([
            'notifications_id' => $this->notification->id,
            'type_id' => $this->data['type_id'],
            'title' => $this->data['title'],
            'description' => $this->data['description'],
            'image' => $this->data['image'],
            'data' => $this->data['data'], // Aquí se almacena el JSON
            'read' => $this->data['read'],
            'status' => 1,
            'read_at'=>Carbon::now(),
            'date_time_at'=>Carbon::now()
        ]);
    }
}
