<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Flight;
use App\Models\Booking; 
use App\Models\Ticket;

class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->generateID(),
            'booking_id' => Booking::inRandomOrder()->first(),
            'passenger' => $this->faker->name(),
        ];
    }

    private function generateID()
    {
        $id = 'T' . strtoupper(Str::random(8));
        $hasDuplicate = Ticket::find($id);

        while($hasDuplicate) {
            $id = 'T' . strtoupper(Str::random(8));
            $hasDuplicate = Ticket::find($id);
        }   

        return $id;
    }

}
