<?php

namespace App\Http\Repositories\Route;

interface RouteRepositoryInterface
{
    public function getAllRoutes();
    public function createRoute(array $data);
    public function editRoute(array $data, int $id);
}