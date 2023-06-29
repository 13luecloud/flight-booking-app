<?php

namespace App\Http\Repositories\Booking;

use App\Models\Booking; 
use App\Models\Flight;

use Illuminate\Support\Facades\Log;

class BookingRepository implements BookingRepositoryInterface
{
    public function getAllBookings()
    {
        /**
         * Initial plan: 
         * If client, only get the bookings for that client
         * If admin, get all bookings 
         * Route for client is different from admin
         * Auth in repository so no ID needs to be passed 
         * Consider using different methods for client and admin for less chances of getting entangled together
        **/
    }

    public function createBooking()
    {
        /**
         * Initial plan: 
         * Create Booking first, then the tickets 
         * Required values => flight_id, user_id, payable, status (default: unpaid)
         * Return created booking
         * Admin probably can't create a booking, since they're the admin. They need to create a client accont. 
         * If booking is created, send email
        **/
    }

    public function editBooking(array $data, String $id)
    {
        /**
         * User cannot edit booking 
         * Admin cannot change:
         * user_id - Cannot changed who booked the booking
         * payables - Affects the number of passengers accounted for
         * If flight_id has changed, recalculate payable
        **/

        $booking = Booking::findOrFail($id);

        if($booking->flight_id !== $data['flight_id']) {
            log::info("In-if");
            $this->updatePayable($booking->flight_id, $data['flight_id'], $booking->id);
            $booking->flight_id = $data['flight_id'];
        }

        $booking->status = $data['status'];
        $booking->save();

        return Booking::find($id);
    }

    public function deleteBooking(String $id)
    {
        /**
         * Admin can delete booking (see: CebuPac)
         * User cannot delete booking (sales are final and irrevocable from their end)
        **/

        $booking = Booking::findOrFail($id);

        $this->deleteBookingRelatedTickets($id);

        $booking->delete();

        return $booking;
    }

    public function updatePayable(int $oldFlightId, int $newFlightId, String $bookingId)
    {
        $oldFlight = Flight::find($oldFlightId);
        $newFlight = Flight::find($newFlightId);
        $booking = Booking::find($bookingId);

        $passengers = $booking->payable / $oldFlight->price;
        log::info($passengers);
        $booking->payable = $passengers * $newFlight->price;
        log::info($booking->payable);

        $booking->save();
    }

    public function deleteBookingRelatedTickets(String $bookingId)
    {
        $booking = Booking::find($bookingId);
        $booking->tickets()->delete();
    }
}