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

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
    }

    public function test_feature_succeed_create_city()
    {
        $data = $this->valid_city_data();

        foreach($data as $city) {
            $response = $this->actingAs($this->user)->post("/api/admin/city", $city);

            $response->assertStatus(200);
            $response->assertJsonCount(3);
            $response->assertJsonStructure([
                'success', 
                'message', 
                'data'
            ]);
        }
    }

    public function test_feature_fail_create_city()
    {
        $data = $this->invalid_city_data();
        
        $response = $this->actingAs($this->user)->post("/api/admin/city", $data[0]);
        $response->assertStatus(500);

        $this->expectException(ValidationException::class);
        $response = $this->actingAs($this->user)->post("/api/admin/city", $data[1]);
        $response->assertStatus(302);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
    }

    public function test_feature_succeed_edit_city()
    {
        $data = $this->valid_city_data();
        $count = count($data);

        foreach($data as $city) {
            $response = $this->actingAs($this->user)->post("/api/admin/city", $city);
        }

        $this->assertDatabaseCount('cities', $count);
        
        $city = City::first();
        $newCity = ['name' => 'New City'];
        $response = $this->actingAs($this->user)->put("/api/admin/city/{$city->id}", $newCity);
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);

        $city = City::where('name', 'Cebu')->first();
        $newCity = ['name' => 'CEBu'];
        $response = $this->actingAs($this->user)->put("/api/admin/city/{$city->id}", $newCity);
        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
    }

    public function test_feature_fail_edit_city_city_exists()
    {
        $data = $this->valid_city_data();
        $count = count($data);

        foreach($data as $city) {
            $response = $this->actingAs($this->user)->post("/api/admin/city", $city);
        }

        $this->assertDatabaseCount('cities', $count);

        $city = City::where('name', '!=', 'Cebu')->first();

        $newCity = ['name' => 'Cebu'];
        $response = $this->actingAs($this->user)->put("/api/admin/city/{$city->id}", $newCity);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
    }

    public function test_function_fail_edit_city_object_does_not_exist()
    {
        $data = $this->valid_city_data();
        $count = count($data);

        foreach($data as $city) {
            $response = $this->actingAs($this->user)->post("/api/admin/city", $city);
        }

        $this->assertDatabaseCount('cities', $count);

        $count++;
        $newCity = ['name' => 'New City'];
        $response = $this->actingAs($this->user)->put("/api/admin/city/{$count}", $newCity);
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
    }

    private function valid_city_data()
    {
        return $data = [
          [
            'name' => 'Cagayan de Oro',
          ],
          [
            'name' => 'Cebu',
          ],
          [
            'name' => 'Manila',
          ],
          [
            'name' => 'Baguio',
          ],
          [
            'name' => 'Iligan',
          ]
        ];
    }

    private function invalid_city_data()
    {
        return $data = [
            [
                'name' => 'Muñoz' 
            ],
            [
                'name' => 'WithNumbers123'
            ]
        ];
    }
}
