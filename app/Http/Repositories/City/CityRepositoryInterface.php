<?php

namespace App\Http\Repositories\City;

interface CityRepositoryInterface 
{
    public function createCity(array $data);
    public function editCity(array $data, int $id);
    public function getAllCities();
}