<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $city = $this->faker->city(); 
        // while($city = "Muñoz" || $city = "Biñan") {
        //     $city = $this->faker->city(); 
        // }

        return [
            'name' => $city,
            'code' => strtoupper(substr($city, 0, 3))
        ];
    }
}
