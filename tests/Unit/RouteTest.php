<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Http\Repositories\Route\RouteRepository;
use App\Models\City;
use App\Models\Route; 

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    private $repository;
    public function setUp(): void 
    {
        parent::setUp();
        $this->repository = app(RouteRepository::class);

        City::factory(5)->create();
    }

    public function test_unit_succeed_get_all_routes()
    {
        Route::factory(5)->create();

        $fetchedRoutes = $this->repository->getAllRoutes();
        $this->assertNotEmpty($fetchedRoutes);
        $this->assertDatabaseCount('routes', 5); 
        foreach($fetchedRoutes as $route) {
            $this->assertModelExists($route);
        }       
    }

    public function test_unit_succeed_create_routes()
    {   // Opted not to use factory because it may generate a duplicate pair of numbers
        $routes = $this->validRoutesData();

        $this->assertDatabaseCount('routes', 0);
        foreach($routes as $route) {
            $createdRoute = $this->repository->createRoute($route);

            $this->assertDatabaseHas('routes', [
                'origin_id' => $route['origin_id'],
                'destination_id' => $route['destination_id']
            ]);
            $this->assertEquals($createdRoute->origin_id, $route['origin_id']);
            $this->assertEquals($createdRoute->destination_id, $route['destination_id']);
        }

        $this->assertDatabaseCount('routes', 3);
    }

    public function test_unit_succeed_update_route()
    {
        $count = 3;
        Route::factory($count)->create();

        $newRoute = $this->routeNonExistentInDatabase();
        while(Route::where([ [ 'origin_id', $newRoute['origin_id'] ], [ 'destination_id', $newRoute['destination_id'] ] ])->exists()) {
            $newRoute = $this->routeNonExistentInDatabase();
        }

        $toUpdateRouteId = Route::inRandomOrder()->first()->id;
        $updatedRoute = $this->repository->editRoute($newRoute, $toUpdateRouteId);

        $this->assertDatabaseCount('routes', $count);
        $this->assertDatabaseHas('routes', [
            'origin_id' => $updatedRoute->origin_id,
            'destination_id' => $updatedRoute->destination_id
        ]);
        $this->assertModelExists($updatedRoute);
    }

    public function test_unit_succeed_delete_route()
    {
        $routes = Route::factory(5)->create();

        foreach($routes as $route) {
            $deletedRoute = $this->repository->deleteRoute($route->id);

            $this->assertEquals($deletedRoute->origin_id, $route->origin_id);
            $this->assertEquals($deletedRoute->destination_id, $route->destination_id);

            $this->assertSoftDeleted($route);
        }
    }

    private function validRoutesData()
    {
        return $data = [
            [
                'origin_id' => 1,
                'destination_id' => 2
            ],
            [
                'origin_id' => 3,
                'destination_id' => 4
            ],
            [
                'origin_id' => 5,
                'destination_id' => 4
            ]
        ];
    }

    private function routeNonExistentInDatabase()
    {
        $originId = rand(1,5);
        $destinationId = rand(1,5);
        
        while ($destinationId === $originId) {
            $destinationId = rand(1,5);
        }

        return [
            'origin_id' => $originId, 
            'destination_id' => $destinationId
        ];
    }
}
