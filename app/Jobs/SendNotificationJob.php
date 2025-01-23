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

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Helpers\ImageHelper;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $app;
    protected $notification;
    protected $userId;
    protected $data;

    public function __construct($notification,$userId,$data)
    {
        $this->app= env('FULL_URL');
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
            'token_user'=>$this->data->token,
            'type_device'=>$this->data->device_type,
            'datetime'=>Carbon::now(),
            'receiver_userId' => $this->userId,
            'sender_userid'=>$this->data->sender_userid,
            'sent_at' => now(),
            'read_at'=>now()
        ]);

        $type = TypeNotification::create([
            'notifications_id' => $this->notification->id,
            'type_id' => $this->data['type_id'],
            'title' => $this->data['title'],
            'description' => $this->data['description'],
            //'image' => $this->data['image'],
            'data' => $this->data['data'], // Aquí se almacena el JSON
            'read' => 0,
            'status' => 1,
            'read_at'=>null,
            'date_time_at'=>Carbon::now()
        ]);

        if(isset($data['image'])&& $data['image'] instanceof UploadedFile){

            ImageHelper::createNotifications($type->id);
            $manager = new ImageManager(new Driver());
            $now=Carbon::now()->format('Ymd_His');
            $notificationsImage=ImageHelper::processImage($data['image'],"notifications/{$type->id}"."{$now}.png",$manager);
            $type->image=$this->app.'/storage' . str_replace('public/', '', $notificationsImage);
            $type->save();

        }


    }
}
