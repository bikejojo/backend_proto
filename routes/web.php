<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sanctum/csrf-cookie', function () {
    Log::info('Cookies recibidas:', request()->cookies->all());
    Log::info('Encabezados recibidos:', request()->headers->all());
    return response()->json(['message' => 'CSRF Cookie Generated']);
});
