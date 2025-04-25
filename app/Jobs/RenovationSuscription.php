<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RenovationSuscription implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected $device;
    protected $title;
    protected $body;
    public function __construct($device , $notification)
    {
        $this->device = $device;
        $this->body = $notification->body;
        $this->title = $notification->title;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $this->device->expo_token,
            'title' => $this->title,
            'body' => $this->body,
        ]);
    }
}
