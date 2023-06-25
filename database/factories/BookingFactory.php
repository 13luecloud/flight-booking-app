<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Flight; 
use App\Models\User; 

class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $flight = Flight::inRandomOrder()->first();
        $passengers = $this->faker->numberBetween(1, 16);

        return [
            'id' =>  'B' . strtoupper(Str::random(8)),
            'flight_id' => $flight->id,
            'user_id' => User::where('role', 'client')->inRandomOrder()->first()->id,
            'payable' => $flight->price * $passengers,
            'status' => $this->faker->randomElement(['paid', 'unpaid'])
        ];
    }
}
