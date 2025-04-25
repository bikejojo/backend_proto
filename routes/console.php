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
//Schedule::command('suscription:disable-expired')->everyThreeMinutes() // Ejecutar cada 3 minutos
  //                                              ->withoutOverlapping();
// uso de manera manual
Artisan::command('subscriptions:disable-expired',function(){
    $this->call(\App\Console\Commands\DisableExpiredSubscriptions::class);
});

Artisan::command('request:Expired',function(){
    $this->call(\App\Console\Commands\UpdateExpiredRequests::class);
});

Artisan::command('app:record-appointments',function(){
    $this->call(\App\Console\Commands\RecordAppointments::class);
});

Artisan::command('app:record-appointments1hr',function(){
    $this->call(\App\Console\Commands\RecordAppointments1hr::class);
});

Artisan::command('app:record-suscription',function(){
    $this->call(\App\Console\Commands\RecordSuscription::class);
});

//Schedule::command('app:record-appointments')->everyMinute() // Ejecutar cada 1 minuto
  //                                              ->withoutOverlapping();
//Schedule::command('app:record-appointments1hr')->everyMinute() // Ejecutar cada 1 minuto
//                                                ->withoutOverlapping();
Schedule::command('app:record-suscription')->everyMinute() // Ejecutar cada 1 minuto
                                                ->withoutOverlapping();
