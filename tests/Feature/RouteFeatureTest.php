<?php

namespace Tests\Feature;

use App\Models\Route;
use App\Models\City;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use Tests\TestCase;

class RouteFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private $cities;
    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory(1)->create(['role' => 'admin']);
        $this->user = User::where('role', 'admin')->first();
        
        $this->cities = City::factory(5)->create();
    }

    public function test_feature_succeed_get_all_routes()
    {
        Route::factory(5)->create();
        $this->assertDatabaseCount('routes', 5);

        $response = $this->actingAs($this->user)->get("/api/admin/route");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
    }

    public function test_feature_succeed_create_route()
    {
        $route = $this->validRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);

        $response->assertStatus(200);
        $response->assertJsonStructure([
        'success', 
        'message', 
        'data'
        ]);

        $this->assertDatabaseHas('routes', [
            'origin_id' => $route['origin_id'],
            'destination_id' => $route['destination_id'],
        ]);
       
    }

    public function test_feature_fail_create_route_duplicate_route()
    {
        $route = $this->validRoute();

        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);
        $this->assertDatabaseHas('routes', [
            [ 'origin_id', $route['origin_id'] ], 
            [ 'destination_id', $route['destination_id'] ]
        ]);

        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);

        $response->assertValid(['origin_id', 'destination_id']);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);        
    }

    public function test_feature_fail_create_route_same_origin_destination()
    {
        $route = $this->duplicateRoute();
        
        $this->expectException(ValidationException::class);
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);
        $this->assertDatabaseMissing('routes', [
            [ 'origin_id', $route['origin_id'] ], 
            [ 'destination_id', $route['destination_id'] ]
        ]);

        $response->assertInvalid('destination_id');
        $response->assertStatus(302);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);      
    }

    public function test_feature_succeed_update_route()
    {
        $route = $this->validRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);
        $routeId = Route::orderBy('id', 'desc')->first()->id;

        $differentRoute = $this->validAnotherRoute();

        $reponse = $this->actingAs($this->user)->put("/api/admin/route/{$routeId}", $differentRoute);
        
        $this->assertDatabaseHas('routes', [
            'origin_id' => $differentRoute['origin_id'], 
            'destination_id' => $differentRoute['destination_id']
        ]);
        $reponse->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);    
    }

    public function test_feature_fail_update_route_not_found()
    {
        $route = $this->validRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);

        $routeId = Route::orderBy('id', 'asc')->first()->id;
        $routeId++;

        $differentRoute = $this->validAnotherRoute();

        $response = $this->actingAs($this->user)->put("/api/admin/route/{$routeId}", $differentRoute);

        $this->assertDatabaseMissing('routes', [
            'origin_id' => $differentRoute['origin_id'], 
            'destination_id' => $differentRoute['destination_id']
        ]);
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);          
    }

    public function test_feature_fail_update_route_duplicate_from_existing_route()
    {
        $route = $this->validRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);
        $this->assertDatabaseHas('routes', [
            'origin_id' => $route['origin_id'], 
            'destination_id' => $route['destination_id']
        ]);

        $secoundRoute = $this->validAnotherRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $secoundRoute);
        $routeId = Route::where([
            'origin_id' => $secoundRoute['origin_id'],
            'destination_id' => $secoundRoute['destination_id']
        ])->first()->id;

        $response = $this->actingAs($this->user)->put("/api/admin/route/{$routeId}", $route);
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
    }

    public function test_feature_fail_update_route_same_origin_destination()
    {
        $route = $this->validRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);
        $routeId = Route::orderBy('id','desc')->first()->id;

        $differentRoute = $this->duplicateRoute();
        $this->expectException(ValidationException::class);
        $response = $this->actingAs($this->user)->put("/api/admin/route/{$routeId}", $differentRoute);
        $this->assertDatabaseMissing('routes', [
            [ 'origin_id', $differentRoute['origin_id'] ], 
            [ 'destination_id', $differentRoute['destination_id'] ]
        ]);

        $response->assertInvalid('destination_id');
        $response->assertStatus(302);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);      
    }

    public function test_feature_succeed_delete_route()
    {
        $route = $this->validRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);
        $route = Route::orderBy('id','desc')->first();

        $response = $this->actingAs($this->user)->delete("/api/admin/route/{$route->id}");

        $this->assertSoftDeleted($route);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]); 
    }

    public function test_feature_fail_delete_route_not_found()
    {
        $route = $this->validRoute();
        $response = $this->actingAs($this->user)->post("/api/admin/route", $route);
        $routeId = Route::orderBy('id','asc')->first()->id;
        $routeId++;

        $response = $this->actingAs($this->user)->delete("/api/admin/route/{$routeId}");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
    }

    private function validRoute()
    {
        $min = City::orderBy('id', 'desc')->first()->id;
        $max = City::orderBy('id', 'asc')->first()->id;

        $originId = rand($min, $max); 
        $destinationId = rand($min, $max);
        while($destinationId === $originId) {
            $destinationId = rand($min, $max);
        }
        
        return $data = [ 'origin_id' => $originId, 'destination_id' => $destinationId ];
    }

    private function validAnotherRoute()
    {
        $data = $this->validRoute();
        while(Route::where([ 
            [ 'origin_id', $data['origin_id'] ], 
            [ 'destination_id', $data['destination_id'] ] 
        ])->exists()) {
            $data = $this->validRoute();
        }

        return $data;
    }

    private function duplicateRoute()
    {
        $min = City::orderBy('id', 'desc')->first()->id;
        return $data = [ 'origin_id' => $min, 'destination_id' => $min ];
    }
}
