<?php

namespace App\Http\Repositories\Flight;

use App\Exceptions\FlightSameScheduleForRouteException;
use App\Models\Flight;
use Carbon\Carbon;

class FlightRepository implements FlightRepositoryInterface
{
    public function getAllFlights()
    {
        return Flight::all();
    }

    public function createFlight(array $data)
    {
        $data['schedule'] = Carbon::createFromFormat('Y-m-d H:i', $data['schedule']);
        $this->isSameScheduleForRoute($data['route_id'], $data['schedule']);
        
        $data['reserved'] = $data['reserved'] ?? 0;  
        return Flight::create($data);
    }

    private function isSameScheduleForRoute(int $routeId, $schedule)
    {
        if(Flight::where([
            'route_id' => $routeId,
            'schedule' => $schedule
        ])->exists()) {
            throw new FlightSameScheduleForRouteException;
        }
    }
}