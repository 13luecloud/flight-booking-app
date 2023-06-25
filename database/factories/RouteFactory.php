<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\City; 

class RouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $cities = $this->getCities();

        return [
            'origin_id' => $cities[0],
            'destination_id' => $cities[1]
        ];
    }

    private function getCities()
    {
        $origin = City::inRandomOrder()->first()->id;
        $destination = City::inRandomOrder()->first()->id;
        
        // in case of condition = true
        while($destination === $origin) {
            $destination = City::inRandomOrder()->first()->id;
        }

        return [$origin, $destination];
    }
}
