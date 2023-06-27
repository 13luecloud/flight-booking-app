<?php

namespace App\Http\Repositories\Route; 

use App\Exceptions\RouteExistsException;
use App\Exceptions\RouteSameOriginDestination;
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
        $this->sameOriginDestination($data['origin_id'], $data['destination_id']);
        
        return Route::create($data);
    }

    public function editRoute(array $data, int $id)
    {
        Route::findOrFail($id);

        $this->isADuplicate($data['origin_id'], $data['destination_id']);
        $this->sameOriginDestination($data['origin_id'], $data['destination_id']);
        
        Route::where('id', $id)->update($data);

        return Route::find($id);
    }

    public function deleteRoute(int $id)
    {
        Route::findOrFail($id);
        
        $this->deleteRelatedChildren($id);
        
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

    private function sameOriginDestination(int $originId, int $destinationId)
    {
        if($originId === $destinationId) {
            throw new RouteSameOriginDestination;
        }
    }

    public function deleteRelatedChildren(int $routeId)
    {
        $route = Route::find($routeId);
        $flights = $route->flights;
        foreach($flights as $flight) {

            $bookings = $flight->bookings;
            foreach($bookings as $booking) {
                $booking->tickets()->delete();
            }

            $flight->bookings()->delete();
        }

        $route->flights()->delete();
    }
}