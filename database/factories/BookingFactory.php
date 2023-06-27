<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Flight; 
use App\Models\User; 
use App\Models\Booking;

class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $id = $this->generateID();
        $flight = Flight::inRandomOrder()->first();
        $passengers = $this->faker->numberBetween(1, 16);

        return [
            'id' =>  $this->generateID(),
            'flight_id' => $flight->id,
            'user_id' => User::where('role', 'client')->inRandomOrder()->first()->id,
            'payable' => $flight->price * $passengers,
            'status' => $this->faker->randomElement(['paid', 'unpaid'])
        ];
    }

    private function generateID()
    {
        $id = 'B' . strtoupper(Str::random(8));
        $hasDuplicate = Booking::find($id);

        while($hasDuplicate) {
            $id = 'B' . strtoupper(Str::random(8));
            $hasDuplicate = Booking::find($id);
        }

        return $id;
    }
}
