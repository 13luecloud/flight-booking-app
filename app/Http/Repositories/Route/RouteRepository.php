<?php

namespace App\Http\Repositories\Route; 

use App\Exceptions\RouteExistsException;
use App\Http\Repositories\Flight\FlightRepository;
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
        $this->isADuplicate($data['origin_id'], $data['destination_id']);
        
        return Route::create($data);
    }

    public function editRoute(array $data, int $id)
    {
        Route::findOrFail($id);

        $this->isADuplicate($data['origin_id'], $data['destination_id']);
        
        Route::where('id', $id)->update($data);

        return Route::find($id);
    }

    public function deleteRoute(int $id)
    {
        Route::findOrFail($id);
        
        $this->deleteRouteRelatedChildren($id);
        
        $data = Route::find($id);
        Route::find($id)->delete();

        return $data;
    }

    private function isADuplicate(int $originID, int $destinationID)
    {
        $route = Route::where([
            ['origin_id', $originID],
            ['destination_id', $destinationID]
        ])->first();

        if($route) {
            throw new RouteExistsException; 
        }
    }

    public function deleteRouteRelatedChildren(int $routeId)
    {
        $route = Route::find($routeId);

        $flightRepo = new FlightRepository;
        $flights = $route->flights;
        foreach($flights as $flight) {
            $flightRepo->deleteFlightRelatedChildren($flight->id);
        }

        $route->flights()->delete();
    }
}