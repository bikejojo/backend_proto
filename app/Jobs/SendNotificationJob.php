<?php

namespace App\Jobs;

use App\Models\Cliente_Interno;
use App\Models\NotificationUser;
use App\Models\NotificationsDevice;
use App\Models\Tecnico;
use App\Models\User;
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
        //dd($this->notification);
        $this->userId = $userId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        //dd($this->notification);
        try {

            Log::info('$this->userId: ' . $this->notification);

            $senderId = is_array($this->data['sender_id'])
                ? $this->data['sender_id'][0]
                : $this->data['sender_id'];

            // Validar y procesar receiver_userid
            $receiverIds = is_array($this->data['recipient_id'])
                ? $this->data['recipient_id']
                : [$this->data['recipient_id']];
            // Guardar cada combinación de sender y receiver
            $senderInfo = $this->findModelTypeById($senderId);

            foreach ($receiverIds as $receiverId) {
                $receiverInfo = $this->findModelTypeById($receiverId);
                NotificationUser::create([
                    'notification_id' => $this->notification->id, // <--- corregido aquí
                    'recipient_id' => $receiverId,
                    'sender_id' => $senderId,
                    'recipient_type' => $receiverInfo['type'],
                    'sender_type' => $senderInfo['type'],
                    'is_read' => false,
                ]);

                if (!empty($this->data['device']) && !empty($this->data['expo_token'])) {
                    NotificationsDevice::create([
                        'device_id' => $this->data['device'],
                        'token' => $this->data['expo_token'],
                        'is_active' => false,
                        'date' => Carbon::now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Registrar el error en los logs
            Log::error('Error en SendNotificationJob: ' . $e->getMessage(), [
                'notification_id' => $this->notification->id,
                'user_id' => $this->userId,
            ]);
            return [
                'message'=>'Errores de notificaciones: ' . $e->getMessage(),
            ];
        }
    }

    private function findModelTypeById($id){
        $tecnico = Tecnico::where('userId',$id)->first();
        if($tecnico) {
            return ['model' => $tecnico, 'type' => Tecnico::class];
        }

        $cliente = Cliente_Interno::where('userId',$id)->first();
        if($cliente){
            return ['model' => $cliente, 'type' => Cliente_Interno::class];
        }

        return ['model' => $id , 'type' =>  User::class];
        // Si no es ni técnico ni cliente interno
        //return null;
    }

}
