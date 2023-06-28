<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Http\Repositories\User\UserRepository; 
use App\Http\Repositories\User\UserRepositoryInterface; 
use App\Http\Repositories\City\CityRepository; 
use App\Http\Repositories\City\CityRepositoryInterface; 
use App\Http\Repositories\Route\RouteRepository; 
use App\Http\Repositories\Route\RouteRepositoryInterface; 
use App\Http\Repositories\Flight\FlightRepository; 
use App\Http\Repositories\Flight\FlightRepositoryInterface;

class RespositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(RouteRepositoryInterface::class, RouteRepository::class);
        $this->app->bind(FlightRepositoryInterface::class, FlightRepository::class);
    }
}
