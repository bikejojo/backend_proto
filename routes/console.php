<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('solicitudes:revisar',function(){
    $this->call(\App\Console\Commands\RevisarSolicitudes::class);
});
