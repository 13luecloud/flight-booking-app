<?php

namespace App\Http\Repositories\City;

use App\Exceptions\CityExistsException;
use App\Http\Repositories\Route\RouteRepository;
use App\Models\City;
use App\Models\Route;

use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Log;
 
class CityRepository implements CityRepositoryInterface 
{
    public function createCity(array $data)
    {  
        $data['code'] = $this->generateCode($data['name']);
        return City::create($data);
    }

    public function deleteCity(int $id)
    {
        $routeRepo = new RouteRepository; 

        $routes = Route::where('origin_id', $id)->orWhere('destination_id', $id)->get();
        foreach($routes as $route) {
            $routeRepo->deleteRelatedChildren($route->id);
            Route::find($route->id)->delete();
        }

        $data = City::find($id);
        City::find($id)->delete();

        return $data;
    }

    public function editCity(array $data, int $id)
    {
        try {
            City::findOrFail($id);
        } catch (\Exception $e) {
            return null;
        }
        
        if($this->isADuplicate($data['name'], $id)) {
            throw new CityExistsException;
        }

        $data['code'] = $this->generateCode($data['name']);
        City::where('id', $id)->update($data);

        return City::find($id);
    }

    public function getAllCities()
    {
        return City::all();
    }

    private function isADuplicate(String $city, int $id)
    {
        $currentCity = str_replace(' ', '', strtoupper($city));

        $savedCities = City::where('id', '<>', $id)->get();
        foreach($savedCities as $city) {
            $savedCity = str_replace(' ', '', strtoupper($city->name));
            
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