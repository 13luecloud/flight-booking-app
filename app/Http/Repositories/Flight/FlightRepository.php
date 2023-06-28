<?php

namespace App\Http\Repositories\Flight;

use App\Models\Flight;

class FlightRepository implements FlightRepositoryInterface
{
    public function getAllFlights()
    {
        return Flight::all();
    }
}