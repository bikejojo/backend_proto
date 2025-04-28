<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Devices;
use App\Models\DevicesUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RequestExpiredSystemd implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected $solicitud;
    protected $diccionary;
    public function __construct($solicitud,$diccionary)
    {
        $this->solicitud = $solicitud;
        $this->diccionary = $diccionary;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::where('id', $this->solicitud->userId)->first();
        $device = DevicesUser::where('userId', $user->id)->first();
        $devices = Devices::where('id', $device->deviceId)->first();
        $diccionary['body'] = str_replace('{fecha}', $this->solicitud->registrationDateTime, $this->diccionary['body']);
        $response = Http::post('https://exp.host/--/api/v2/push/send',[
            'to' => $devices->expo_token,
            'title' => $diccionary['title'],
            'body' => $diccionary['body'],
        ]);
    }
}
