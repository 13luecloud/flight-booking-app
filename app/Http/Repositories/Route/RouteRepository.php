<?php

namespace App\Http\Repositories\Route; 

use App\Exceptions\RouteExistsException;
use App\Models\Route; 

use Illuminate\Support\Facades\Log;

class RouteRepository implements RouteRepositoryInterface
{
    public function getAllRoutes()
    {
        return Route::all();
    }

    public function createRoute(array $data)
    {
        if($this->isADuplicate($data['origin_id'], $data['destination_id'])) {
            throw new RouteExistsException; 
        } 
        
        return Route::create($data);
    }

    private function isADuplicate(int $originID, int $destinationID)
    {
        $route = Route::where([
            ['origin_id', $originID],
            ['destination_id', $destinationID]
        ])->first();

        if($route) {
            return true;
        }
            return false;
    }
}