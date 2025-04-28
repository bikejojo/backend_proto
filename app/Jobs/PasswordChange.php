<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;


class PasswordChange implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public $devices;
    public $notifications;

    public function __construct($devices ,$notifications)
    {
        $this->devices = $devices;
        $this->notifications = $notifications;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $this->devices->expo_token,
            'title' => $this->notifications->title,
            'body' => $this->notifications->body,
            'data' => json_decode($this->notifications->data, true),
        ]);
    }
}
