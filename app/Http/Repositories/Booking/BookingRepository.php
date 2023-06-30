<?php

namespace App\Http\Repositories\Booking;

use App\Exceptions\BookingPassengersGreaterThanCapacity;
use App\Http\Repositories\Ticket\TicketRepository;
use App\Mail\BookingConfirmed;
use App\Models\Booking; 
use App\Models\Flight;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingRepository implements BookingRepositoryInterface
{
    private $sendMail;
    public function __construct()
    {
      $this->sendMail = config('app.send_mail');
    }

    public function getAllBookings()
    {
        return Booking::all();
    }

    public function getAllUserBookings()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)->get();
        
        return $bookings;
    }

    public function createBooking(array $data)
    {
        /**
         * Client can create booking for X passengers 
         * Booking sales are final and irrevocable 
         * Booking cannot be pushed if (flight's reserved + booking passengers) > flight's capacity
         * Admin cannot create booking, they need a client account  
        **/

        $user = Auth::user();
        $flight = Flight::find($data['flight_id']);
               
        $this->canAccommodatePassengers($flight, $data['passengers']);
        $this->updateFlightReserved($flight, $data['passengers']);

        $booking = [];
        $booking['id'] = $this->generateBookingId();
        $booking['flight_id'] = $data['flight_id'];
        $booking['user_id'] = $user->id;
        $booking['payable'] = $this->calculatePayable($flight, $data['passengers']);
        $booking['status'] = 'unpaid';
        Booking::create($booking);

        $ticket = new TicketRepository;
        $ticket->createTickets($data['passengers'], $booking['id']);

        if($this->sendMail) {
            $this->sendMail($user->name, $user->email, $booking['id'], $booking['payable']);
        }

        return Booking::find($booking['id']);
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

    private function canAccommodatePassengers(Flight $flight, array $passengers)
    {
        $passengers = count($passengers);
        if( ($flight->reserved + $passengers) > $flight->capacity ) {
            throw new BookingPassengersGreaterThanCapacity;
        }
    }

    private function generateBookingId()
    {
        $bookingId = 'B' . strtoupper(Str::random(8));
        $hasDuplicate = Booking::find($bookingId);

        while($hasDuplicate) {
            $bookingId = 'B' . strtoupper(Str::random(8));
            $hasDuplicate = Booking::find($bookingId);
        }

        return $bookingId;
    }

    private function calculatePayable(Flight $flight, array $passengers)
    {
        return $flight->price * count($passengers);
    }

    private function updatePayable(int $oldFlightId, int $newFlightId, String $bookingId)
    {
        $oldFlight = Flight::find($oldFlightId);
        $newFlight = Flight::find($newFlightId);
        $booking = Booking::find($bookingId);

        $passengers = $booking->payable / $oldFlight->price;
        $booking->payable = $passengers * $newFlight->price;

        $booking->save();
    }

    private function updateFlightReserved(Flight $flight, array $passengers)
    {
        $flight->reserved = $flight->reserved + count($passengers);
        $flight->save();
    }

    private function deleteBookingRelatedTickets(String $bookingId)
    {
        $booking = Booking::find($bookingId);
        $booking->tickets()->delete();
    }

    private function sendMail(String $name, String $email, String $bookingId, int $payable)
    {
        Mail::to($email)->send(new BookingConfirmed($name, $bookingId, $payable));
    }
}