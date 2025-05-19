<?php

namespace App\Events;

use App\Models\Publicidad;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PublicidadEnvio
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    protected $publicidad;
    protected $techsids;
    protected $admin;
    protected $clientIds;

    public function __construct(Publicidad $publicida, $techIds, $admin,$clientIds)
    {
        $this->publicidad = $publicida;
        $this->techsids   = $techIds;
        $this->admin      = $admin;
        $this->clientIds  = $clientIds;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

    public function getPublicidad(){
        return $this->publicidad;
    }

    public function getTechsIds(){
        return $this->techsids;
    }

    public function getAdmin(){
        return $this->admin;
    }

    public function getClientIds(){
        return $this->clientIds;
    }
}
