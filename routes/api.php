<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*Route::middleware([EnsureFrontendRequestsAreStateful::class, 'api'])
    ->post('/graphql', '\Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController@query');
*/
