<?php

namespace App\Jobs;


use App\Models\User;
use App\Models\Devices;
use App\Models\Tecnico;
use App\Models\Servicio;
use App\Models\DevicesUser;
use App\Models\Notification;
use App\Models\Cliente_Interno;
use App\Models\NotificationUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class recordAgenda implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected $service;
    protected $user;
    protected $config;

    public function __construct( $service, $user, $config)
    {
        $this->service = $service;
        $this->user = $user;
        $this->config = $config;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $deviseUser = DevicesUser::where('users_id', $this->user->id)->first();
            $devices = Devices::where('id', $deviseUser->device_id)->first();
            $response = Http::post('https://exp.host/--/api/v2/push/send', [
                'to' => $devices->expo_token,
                'title' => $this->config['title'],
                'body' => $this->config['body'],
            ]);

        } catch (\Exception $e) {
            // Manejo de excepciones
            Log::error('Error en el trabajo recordAgenda: ' . $e->getMessage());
        }
    }
}
