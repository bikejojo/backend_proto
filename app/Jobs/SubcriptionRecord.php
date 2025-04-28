<?php

namespace App\Jobs;


use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;


class SubcriptionRecord implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $body;
    protected $title;
    protected $device;
    
    public function __construct($device, $title, $body)
    {
        $this->body = $body;
        $this->title = $title;
        $this->device = $device;
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
