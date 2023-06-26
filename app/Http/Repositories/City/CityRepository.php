<?php

namespace App\Http\Repositories\City;

use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Log;

use App\Models\City; 

class CityRepository implements CityRepositoryInterface 
{
    public function createCity(array $data)
    {  
        
        $data['code'] = $this->generateCode($data['name']);
        $city = City::create($data);
        return response()->success(
            'Successfully created City', 
            [
                'city' => $city,
            ]
        );

    }

    public function editCity(array $data, int $id)
    {
        if($this->isADuplicate($data['name'], $id)) {
            return response()->fail(
                'The given data was invalid',
                [
                    'city' => ['City already exists']
                ], 
                422
            );
        }

        $data['code'] = $this->generateCode($data['name']);
        City::where('id', $id)->update($data);

        try {
            $city = City::findOrFail($id);
            return response()->success(
                'Successfully updated ' . $city->name . ' City', 
                [
                    'city' => $city
                ]
            );
        } catch(ModelNotFoundException $e) {
            return response()->fail(
                'No query results for City',
                [
                    'city' => ['City does not exists']
                ],
                404
            );
        }
    }

    public function getAllCities()
    {
        $cities = City::all();
        return response()->success(
            'Successfully fetched all Cities',
            [
                'cities' => $cities
            ]
        );
    }

    private function isADuplicate(String $city, int $id)
    {
        $currentCity = strtoupper($city);
        $savedCities = City::where('id', '<>', $id)->get();
        foreach($savedCities as $city) {
            $savedCity = strtoupper($city->name);
            $savedCity = str_replace(' ', '', $savedCity);

            if ($currentCity === $savedCity) {
                return true;
            }
        }

        return false;
    } 

    private function generateCode(String $city)
    {
        return $code = strtoupper(substr($city, 0, 3));
    }
}