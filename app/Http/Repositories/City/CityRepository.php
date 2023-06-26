<?php

namespace App\Http\Repositories\City;

use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Log;

use App\Models\City; 

class CityRepository implements CityRepositoryInterface 
{
    public function createCity(array $data)
    {        
        $doesExists = $this->doesExists($data['name']);
        if ($doesExists) {
            return response()->fail(
                'The given data was invalid', 
                [
                    'name' => ['The name ' . $data['name'] . ' City already exists']
                ],
                422
            );
        }

        $code = strtoupper(substr($data['name'], 0, 3));
        $data['code'] = $code;
        $city = City::create($data);
        return response()->success(
            'Successfully created City', 
            [
                'city' => $city,
            ]
        );

    }
    
    private function doesExists(String $city)
    {
        $currentCity = strtoupper($city);
        foreach(City::all() as $city) {
            $savedCity = strtoupper($city->name);
            $savedCity = str_replace(' ', '', $savedCity);

            if ($currentCity === $savedCity) {
                return true;
            }
        }

        return false;
    }
}