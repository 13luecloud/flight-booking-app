<?php

namespace App\Http\Repositories\Route;

interface RouteRepositoryInterface
{
    public function getAllRoutes();
    public function createRoute(array $data);
}