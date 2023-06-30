<?php

namespace App\Http\Repositories\Flight;

use App\Exceptions\FlightSameScheduleForRouteException;
use App\Exceptions\FlightLessThanReserved;
use App\Models\Booking;
use App\Models\Flight;

use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

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

    public function editFlight(array $data, int $id)
    {
        $flight = Flight::findOrFail($id);

        // String to DateTime
        $data['schedule'] = Carbon::createFromFormat('Y-m-d H:i', $data['schedule']);
        $this->isSameScheduleForRoute($data['route_id'], $data['schedule']);

        $this->isLessThanCurrentlyReserved($id, $data['capacity'], 'capacity');
        $this->isLessThanCurrentlyReserved($id, $data['reserved'], 'reserved');

        $this->updateBookingPayable($id, $flight->price, $data['price']);

        Flight::where('id', $id)->update($data);

        return Flight::find($id);
    }

    public function deleteFlight(int $id)
    {
        $booking = Flight::findOrFail($id);

        $this->deleteFlightRelatedChildren($id);

        $booking->delete();

        return $booking;
    }

    public function deleteFlightRelatedChildren(int $flightId)
    {
        $flight = Flight::find($flightId);
        
        $bookings = $flight->bookings;
        foreach($bookings as $booking) {
            $booking->tickets()->delete();
        }
        $flight->bookings()->delete();
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

    private function isLessThanCurrentlyReserved(int $flightId, int $newValue, String $attribute)
    {
        $flight = Flight::find($flightId);
        if($flight->reserved > $newValue) {
            throw new FlightLessThanReserved($attribute);
        }
    }

    private function updateBookingPayable(int $flightId, int $oldPrice, int $newPrice)
    {
        if($oldPrice !== $newPrice) {
            $flight = Flight::find($flightId);

            $bookings = $flight->bookings;
            foreach($bookings as $booking) {
                $reservations = $booking->payable / $oldPrice;
                $booking->payable = $reservations * $newPrice;

                $booking->save();
            }
        }
    }
}