<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Route;

class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        $capacity = $this->faker->numberBetween(99, 800);
        
        return [
            'route_id' => Route::inRandomOrder()->first()->id,
            'capacity' => $capacity, 
            'reserved' => $this->faker->numberBetween(0, $capacity),
            'price' => $this->faker->numberBetween(999, 20001),
            'schedule'  => $this->faker->dateTime()->format('Y-m-d H:i'),
        ];
    }
}
