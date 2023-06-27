<?php

namespace App\Http\Repositories\Route; 

use App\Models\Route; 
use Illuminate\Support\Facades\Log;
class RouteRepository implements RouteRepositoryInterface
{
    public function getAllRoutes()
    {
        return Route::all();
    }
}