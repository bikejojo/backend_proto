<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SolicitudCreada;
use App\Events\SolicitudCancelada;
use App\Events\SolicitudAceptada;

use App\Listeners\NotificarSolicitudCreada;
use App\Listeners\NotificarSolicitudCancelada;
use App\Listeners\NotificarSolicitudAceptada;


class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected $listen = [
        SolicitudCreada::class => [
            NotificarSolicitudCreada::class,
        ],

        SolicitudCancelada::class => [
            NotificarSolicitudCancelada::class,
        ],

        SolicitudAceptada::class => [
            NotificarSolicitudAceptada::class,
        ],
    ];
}
