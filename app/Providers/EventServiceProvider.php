<?php

namespace App\Providers;

use App\Events\ServicioAnulado;
use App\Events\PublicidadEnvio;
use App\Events\SolicitudCreada;
use App\Events\ServicioTerminado;
use App\Events\SolicitudAceptada;
use App\Events\SolicitudCancelada;
use App\Events\ServicioCompletado;
use App\Listeners\NotificarServicioAnulado;
use App\Listeners\NotificarPublicidadEnvio;
use App\Listeners\NotificarSolicitudCreada;
use App\Listeners\NotificarServicioTerminado;
use App\Listeners\NotificarSolicitudAceptada;
use App\Listeners\NotificarSolicitudCancelada;
use App\Listeners\NotificarServicioCompletado;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


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

        ServicioAnulado::class => [
            NotificarServicioAnulado::class,
        ],

        ServicioCompletado::class => [
            NotificarServicioCompletado::class,
        ],

        ServicioTerminado::class => [
            NotificarServicioTerminado::class,
        ],

        PublicidadEnvio::class => [
            NotificarPublicidadEnvio::class,
        ],
    ];
}
