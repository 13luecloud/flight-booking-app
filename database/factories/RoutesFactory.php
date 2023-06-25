<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoutesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'origin' => 'Origin',
            'destination' => 'Destination'
        ];
    }

    /**
     * Sets a singular route; use place 3-letter CODE
     *
     * @return array
     */
    public function setPlaces($origin, $destination)
    {
        return [
            'origin' => $origin,
            'destination' => $destination
        ];
    }
}
