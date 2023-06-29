<?php

namespace App\Http\Repositories\Ticket; 

use App\Models\Ticket; 

use Illuminate\Support\Str;

class TicketRepository
{
    public function createTickets(array $passengers, String $bookingId)
    {
        foreach($passengers as $passenger) {
            $ticket = [];
            $ticket['id'] = $this->generateTicketId();
            $ticket['booking_id'] = $bookingId;
            $ticket['passenger'] = $passenger;

            Ticket::create($ticket);
        }
    }

    private function generateTicketId()
    {
        $ticketId = 'T' . strtoupper(Str::random(8));
        $hasDuplicate = Ticket::find($ticketId);

        while($hasDuplicate) {
            $ticketId = 'T' . strtoupper(Str::random(8));
            $hasDuplicate = Ticket::find($ticketId);
        }   

        return $ticketId;
    }
}