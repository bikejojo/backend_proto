<?php

namespace App\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class PublicidadEnvioTech implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public $notification;
    public $notificationUser;
    public $devices;

    public function __construct($notification,$notificationUser,$device)
    {
        $this->notification = $notification;
        $this->notificationUser = $notificationUser;
        $this->devices = $device;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $response = Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $this->devices->expo_token,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            //'data' => json_decode($this->notifications->data, true),
        ]);

        $this->notificationUser->expo_response = $response;
        $this->notificationUser->save();
    }
}
