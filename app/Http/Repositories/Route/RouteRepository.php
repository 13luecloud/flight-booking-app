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

    public function editRoute(array $data, int $id)
    {
        try {
            Route::findOrFail($id);
        } catch (\Exception $e) {
            return null;
        }

        if($this->isADuplicate($data['origin_id'], $data['destination_id'])) {
            throw new RouteExistsException; 
        } 

        Route::where('id', $id)->update($data);

        return Route::find($id);
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