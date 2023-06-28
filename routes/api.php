<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(UserController::class)->group(function() {
    Route::post('/register', 'store');
    Route::post('/login', 'login');
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    // Shared routes
    Route::group(['middleware' => ['role:admin,client']], function(){
        Route::apiResource('flight', FlightController::class)->only(['index']);
    });    

    //Role-based routes
    Route::group(['prefix' => 'admin', 'middleware' => ['role:admin']], function() {
        Route::apiResource('city', CityController::class);
        Route::apiResource('route', RouteController::class);
        Route::apiResource('flight', FlightController::class)->except(['index']);
    });
});