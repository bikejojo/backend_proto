<?php

namespace App\Jobs;

use App\Models\Devices;
use App\Models\DevicesUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class recordAgenda implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $service;
    protected $user;
    protected $config;

    public function __construct($service, $user, $config)
    {
        $this->service = $service;
        $this->user = $user;
        $this->config = $config;
    }

    public function handle(): void
    {
        try {
            $deviceUsers = DevicesUser::where('users_id', $this->user->id)->get();

            if ($deviceUsers->isEmpty()) {
                Log::warning("No se encontraron dispositivos para el usuario ID: {$this->user->id}");
                return;
            }

            $messages = [];

            foreach ($deviceUsers as $deviceUser) {
                $device = Devices::find($deviceUser->device_id);
                if ($device && $device->expo_token) {
                    $messages[] = [
                        'to' => $device->expo_token,
                        'title' => $this->config['title'],
                        'body' => $this->config['body'],
                        'data' => [
                            'service_id' => $this->service->id,
                        ],
                    ];
                }
            }

            if (!empty($messages)) {
                // Puedes enviar hasta 100 mensajes en un solo POST
                $chunks = array_chunk($messages, 100);

                foreach ($chunks as $chunk) {
                    $response = Http::post('https://exp.host/--/api/v2/push/send', $chunk);
                    Log::info('Push response: ' . json_encode($response->json()));
                }
            }
        } catch (\Exception $e) {
            Log::error('Error en el trabajo recordAgenda: ' . $e->getMessage());
        }
    }
}
