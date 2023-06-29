<?php

namespace App\Http\Repositories\Booking;

use App\Models\Booking; 

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

    public function editBooking()
    {
        /**
         * User cannot edit booking 
         * Admin cannot change the user_id and the payables (affects the number of passengers booked for that booking) only 
         * If flight_id has changed, recalculate payable and turn status into unpaid(?) 
        **/
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

    public function deleteBookingRelatedTickets(String $bookingId)
    {
        $booking = Booking::find($bookingId);
        $booking->tickets()->delete();
    }
}