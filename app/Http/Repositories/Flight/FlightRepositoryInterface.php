<?php

namespace App\Http\Repositories\Flight;

interface FlightRepositoryInterface
{
    public function getAllFlights();
    public function createFlight(array $data);
}