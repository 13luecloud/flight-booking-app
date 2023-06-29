<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking; 
use App\Models\Flight;
use App\Models\Ticket; 

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $booking = Booking::inRandomOrder()->first();
        $flight = Flight::find($booking->flight_id);
        $passengers = $booking->payable / $flight->price;

        for($count = 0; $count < $passengers; $count++) {
            Ticket::factory(1)->create(['booking_id' => $booking->id]);
        }
    }
}
