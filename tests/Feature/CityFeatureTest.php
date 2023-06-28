<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Models\City;
use App\Models\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CityFeatureTest extends TestCase
{   
    use RefreshDatabase;

    private User $user;
    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory(1)->create(['role' => 'admin']);
        $this->user = User::where('role', 'admin')->first();
    }

    public function test_feature_succeeds_get_all_cities()
    {
        $response = $this->actingAs($this->user)->get("/api/admin/city");
        
        $this->assertDatabaseCount('cities', 0);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['data'], []);

        City::factory(3)->create();

        $response = $this->actingAs($this->user)->get("/api/admin/city");

        $this->assertDatabaseCount('cities', 3);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertCount(3, $responseBody['data']);
    }

    public function test_feature_succeed_create_city()
    {
        $cities = City::factory(3)->make();

        $this->assertDatabaseCount('cities', 0);

        foreach($cities as $city) {
            $response = $this->actingAs($this->user)->post("/api/admin/city", $city->toArray());

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'success', 
                'message', 
                'data'
            ]);
        }

        $this->assertDatabaseCount('cities', 3);
    }

    public function test_feature_fail_create_city()
    {
        $city = City::factory(1)->make([
            'name' => 'Muñoz'
        ]);

        $response = $this->actingAs($this->user)->post("/api/admin/city", $city[0]->toArray());
        $response->assertStatus(500);
        $this->assertDatabaseCount('cities', 0);

        $city = City::factory(1)->make([
            'name' => 'WithNumbers1233'
        ]);

        $this->expectException(ValidationException::class);
        $response = $this->actingAs($this->user)->post("/api/admin/city", $city[0]->toArray());
        $response->assertStatus(302);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
    }

    public function test_feature_succeed_edit_city()
    {
        City::factory(5)->create();

        $this->assertDatabaseCount('cities', 5);
        
        $city = City::first();
        $newCity = ['name' => 'New City'];
        $response = $this->actingAs($this->user)->put("/api/admin/city/{$city->id}", $newCity);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $this->assertDatabaseHas('cities', [
            'name' => $newCity['name']
        ]);

        $newCity = ['name' => strtoupper($newCity['name'])];
        $response = $this->actingAs($this->user)->put("/api/admin/city/{$city->id}", $newCity);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $this->assertDatabaseHas('cities', [
            'name' => strtoupper($newCity['name'])
        ]);
    }

    public function test_feature_fail_edit_city_city_exists()
    {
        City::factory(5)->create();

        $this->assertDatabaseCount('cities', 5);

        $city = City::first();
        $cityId = ($city->id) + 1;

        $newCity = ['name' => $city->name];
        $response = $this->actingAs($this->user)->put("/api/admin/city/{$cityId}", $newCity);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);

        $responseBody = $response->json();
        $this->assertEquals($responseBody['errors']['city'], 'City already exists');
    }

    public function test_function_fail_edit_city_object_does_not_exist()
    {
        City::factory(3)->create();

        $this->assertDatabaseCount('cities', 3);

        $newCity = ['name' => 'New City'];
        $response = $this->actingAs($this->user)->put("/api/admin/city/4", $newCity);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $this->assertDatabaseMissing('cities', [
            'name' => 'New City'
        ]);
    }

    public function test_function_succeed_delete_city()
    {
        $city = City::factory(1)->create();

        $this->assertDatabaseCount('cities', 1);

        $response = $this->actingAs($this->user)->delete("/api/admin/city/{$city[0]->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $this->assertSoftDeleted($city[0]);
        $this->assertDatabaseCount('cities', 1);
    }

    public function test_function_fail_delete_city_object_does_not_exist()
    {
        City::factory(3)->create();

        $this->assertDatabaseCount('cities', 3);

        $response = $this->actingAs($this->user)->delete("/api/admin/city/4");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
    }
}
