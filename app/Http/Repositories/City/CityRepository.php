<?php

namespace App\Http\Repositories\City;

use App\Exceptions\CityExistsException;

use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Log;

use App\Models\City; 

class CityRepository implements CityRepositoryInterface 
{
    public function createCity(array $data)
    {  
        $data['code'] = $this->generateCode($data['name']);
        return City::create($data);
    }

    public function editCity(array $data, int $id)
    {
        City::findOrFail($id);

        if($this->isADuplicate($data['name'], $id)) {
            throw new CityExistsException;
        }

        $data['code'] = $this->generateCode($data['name']);
        City::where('id', $id)->update($data);

        return City::findOrFail($id);
    }

    public function getAllCities()
    {
        return $cities = City::all();
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