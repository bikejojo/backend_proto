<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


//Artisan::command('solicitudes:revisar',function(){
  //  $this->call(\App\Console\Commands\RevisarSolicitudes::class);
//});

Artisan::command('update:Expired',function(){
    $this->call(\App\Console\Commands\UpdateExpiredPublicity::class);
});

// uso de tareas programas
Schedule::command('subcription:disable-expired')->daily();
// uso de manera manual
Artisan::command('suscripcion:Expired',function(){
    $this->call(\App\Console\Commands\DisableExpiredSubscriptions::class);
});

Artisan::command('request:Expired',function(){
    $this->call(\App\Console\Commands\UpdateExpiredRequests::class);
});
