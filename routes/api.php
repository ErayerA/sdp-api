<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/routes', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => $route->middleware(),
            'methods' => $route->methods(),
        ];
    });

    return response()->json($routes);
});

Route::middleware('auth:api')->namespace('App\Http\Controllers\Api')->group(function () {

    // istenen route /register olduğu için konvansiyon dışına çıkıldı:
    Route::post('register', 'UserController@store');

    // istenen endpoint'lere göre metodlar sınırlandırıldı:
    Route::apiResource('user', 'UserController')->only(['show', 'index']);
    Route::apiResource('user.subscriptions', 'UserSubscriptionController')->only(['store','update','destroy']);
    Route::apiResource('user.transactions', 'UserTransactionController')->only(['store']);
});
