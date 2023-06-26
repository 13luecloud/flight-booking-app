<?php

namespace App\Http\Repositories\City;

interface CityRepositoryInterface 
{
    public function createCity(array $data);
    public function getAllCities();
}