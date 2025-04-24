<?php

namespace App\Providers;


use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SolicitudCreada;
use App\Events\SolicitudCancelada;
use App\Events\SolicitudAceptada;
use App\Events\ServicioAnulado;
use App\Events\ServicioCompletado;
use App\Events\ServicioTerminado;

use App\Listeners\NotificarSolicitudCreada;
use App\Listeners\NotificarSolicitudCancelada;
use App\Listeners\NotificarSolicitudAceptada;
use App\Listeners\NotificarServicioAnulado;
use App\Listeners\NotificarServicioCompletado;
use App\Listeners\NotificarServicioTerminado;


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
    ];
}
