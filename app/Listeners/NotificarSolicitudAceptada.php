<?php

namespace App\Listeners;

use App\Events\SolicitudAceptada;
use Carbon\Carbon;
use App\Services\DiccionaryNotifications;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarSolicitudAceptada
{
    /**
     * Create the event listener.
     */

    protected $now;

    public function __construct()
    {
        $this->now = Carbon::now();
    }

    /**
     * Handle the event.
     */
    public function handle(SolicitudAceptada $event): void
    {
        $service = $event->service;
        $actionKey = 'request_accepted';
        $config = DiccionaryNotifications::getByKey($actionKey);
        
    }
}
