<?php

namespace App\Http\Repositories\Flight;

interface FlightRepositoryInterface
{
    public function getAllFlights();
    public function createFlight(array $data);
    public function editFlight(array $data, int $id);
    public function deleteFlight(int $id);
}