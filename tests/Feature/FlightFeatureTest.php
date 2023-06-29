<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\City;
use App\Models\Flight;
use App\Models\User;
use App\Models\Route; 

use Carbon\Carbon;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use Tests\TestCase;

class FlightFeatureTest extends TestCase
{
    use RefreshDatabase; 

    private User $admin;
    private int $cityCount = 3;
    private int $routeCount = 3;
    public function setUp(): void
    {
        parent::setUp();
        $admin = User::factory(1)->create(['role' => 'admin']);
        $this->admin = User::where('role', 'admin')->first();

        City::factory($this->cityCount)->create();
        Route::factory($this->routeCount)->create();
    }

    public function test_feature_succeed_client_get_all_flights()
    {
        User::factory(1)->create();
        $client = User::where('role', 'client')->first();
        
        $response = $this->actingAs($client)->get("/api/flight");

        $this->assertDatabaseCount('flights', 0);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['data'], []);

        Flight::factory(3)->create();

        $response = $this->actingAs($client)->get("/api/flight");
        $this->assertDatabaseCount('flights', 3);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertCount(3, $responseBody['data']);
    }

    public function test_feature_succeed_admin_get_all_flights()
    {        
        $response = $this->actingAs($this->admin)->get("/api/flight");

        $this->assertDatabaseCount('flights', 0);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['data'], []);

        Flight::factory(3)->create();

        $response = $this->actingAs($this->admin)->get("/api/flight");
        $this->assertDatabaseCount('flights', 3);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertCount(3, $responseBody['data']);
    }

    public function test_feature_succeed_admin_create_flight()
    {
        $flights = Flight::factory(3)->make();
        
        foreach($flights as $flight) {
            $flight->schedule = Carbon::parse($flight->schedule)->format('Y-m-d H:i');
            $flight = $flight->toArray();

            $response = $this->actingAs($this->admin)->post("/api/admin/flight", $flight);

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'success', 
                'message', 
                'data'
            ]);
            $this->assertDatabaseHas('flights', [
                'route_id'  => $flight['route_id'],
                'capacity'  => $flight['capacity'],
                'reserved'  => $flight['reserved'],
                'price'     => $flight['price'],
                'schedule'  => $flight['schedule']
            ]);
        }

        $this->assertDatabaseCount('flights', 3);
    }

    public function test_feature_fail_admin_create_flight_validation_errors()
    {
        //Incomplete because testing stops after the first call to test (see: 134)
        //Expecting 8 assertions in this function, but received 4; why? LORD 
        $flight = Flight::factory(1)->make([
            'route_id' => null,
        ]);        
        $flight[0]->schedule = Carbon::parse($flight[0]->schedule)->format('Y-m-d H:i');
        $flight = $flight->toArray();
        $this->test_validation_errors($flight, 'create', 'route_id');

        $flight = Flight::factory(1)->make([
            'capacity' => 'null'
        ]);        
        $flight[0]->schedule = Carbon::parse($flight[0]->schedule)->format('Y-m-d H:i');
        $flight = $flight->toArray();
        $this->test_validation_errors($flight, 'create', 'capacity');
    }
 
    public function test_feature_fail_admin_create_flight_same_schedule_for_route()
    {
        $flight = Flight::factory(1)->create();
        $newFlight = Flight::factory(1)->make([
            'route_id' => $flight[0]->route_id,
            'schedule' => $flight[0]->schedule
        ]);

        $newFlight[0]->schedule = Carbon::parse($newFlight[0]->schedule)->format('Y-m-d H:i');
        $newFlight = $newFlight[0]->toArray();

        $response = $this->actingAs($this->admin)->post("/api/admin/flight", $newFlight);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['errors']['flight'], 'Flight with the same route and schedule exists');
    }

    public function test_feature_succeed_admin_edit_flight()
    {
        $flight = Flight::factory(1)->create();
        $flightId = $flight[0]->id;

        $this->assertDatabaseCount('flights', 1);
        $this->assertDatabaseHas('flights', [
            'id'        => $flightId,
            'route_id'  => $flight[0]['route_id'],
            'capacity'  => $flight[0]['capacity'],
            'reserved'  => $flight[0]['reserved'],
            'price'     => $flight[0]['price'],
            'schedule'  => $flight[0]['schedule'],
        ]);

        $newFlight = Flight::factory(1)->make();
        $newFlight = $newFlight[0]->toArray();

        $response = $this->actingAs($this->admin)->put("/api/admin/flight/{$flightId}", $newFlight);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $this->assertDatabaseHas('flights', [
            'id'        => $flightId,
            'route_id'  => $newFlight['route_id'],
            'capacity'  => $newFlight['capacity'],
            'reserved'  => $newFlight['reserved'],
            'price'     => $newFlight['price'],
            'schedule'  => $newFlight['schedule'],
        ]);
    }

    public function test_feature_fail_admin_edit_flight_not_found()
    {
        $flight = Flight::factory(1)->create();
        $flightId = $flight[0]->id;

        $this->assertDatabaseCount('flights', 1);
        $this->assertDatabaseHas('flights', [
            'id'        => $flightId,
            'route_id'  => $flight[0]['route_id'],
            'capacity'  => $flight[0]['capacity'],
            'reserved'  => $flight[0]['reserved'],
            'price'     => $flight[0]['price'],
            'schedule'  => $flight[0]['schedule'],
        ]);

        $newFlight = Flight::factory(1)->make();
        $newFlight = $newFlight[0]->toArray();

        $flightId++;
        $response = $this->actingAs($this->admin)->put("/api/admin/flight/{$flightId}", $newFlight);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $flightId--;
        $this->assertDatabaseHas('flights', [
            'id'        => $flightId,
            'route_id'  => $flight[0]['route_id'],
            'capacity'  => $flight[0]['capacity'],
            'reserved'  => $flight[0]['reserved'],
            'price'     => $flight[0]['price'],
            'schedule'  => $flight[0]['schedule'],
        ]);
    }

    public function test_feature_fail_admin_edit_flight_same_schedule_for_route()
    {
        $flight = Flight::factory(2)->create();
        $flightId = $flight[0]->id;

        $this->assertDatabaseCount('flights', 2);

        $newFlight = Flight::factory(1)->make([
            'route_id' => $flight[1]->route_id,
            'schedule' => $flight[1]->schedule
        ]);

        $newFlight[0]->schedule = Carbon::parse($newFlight[0]->schedule)->format('Y-m-d H:i');
        $newFlight = $newFlight[0]->toArray();

        $response = $this->actingAs($this->admin)->put("/api/admin/flight/{$flightId}", $newFlight);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['errors']['flight'], 'Flight with the same route and schedule exists');
        $this->assertDatabaseHas('flights', [
            'id'        => $flightId,
            'route_id'  => $flight[0]['route_id'],
            'capacity'  => $flight[0]['capacity'],
            'reserved'  => $flight[0]['reserved'],
            'price'     => $flight[0]['price'],
            'schedule'  => $flight[0]['schedule'],
        ]);
    }

    public function test_feature_fail_admin_edit_flight_capacity_less_than_currently_reserved()
    {
        User::factory(5)->create();

        $flight = Flight::factory(1)->create([
            'capacity' => 150,
            'reserved' => 100,
        ]);
        $flightId = $flight[0]->id;
        $passengers = rand(1, $flight[0]->capacity);

        Booking::factory(1)->create([
            'flight_id' => $flightId,
            'payable'   => $passengers * $flight[0]->price
        ]);

        $newCapacity = rand(1, $passengers--);
        $newFlight = Flight::factory(1)->make([
            'capacity' => $newCapacity, 
            'reserved' => $newCapacity--,
        ]);
        $newFlight[0]->schedule = Carbon::parse($newFlight[0]->schedule)->format('Y-m-d H:i');
        $newFlight = $newFlight[0]->toArray();

        $response = $this->actingAs($this->admin)->put("/api/admin/flight/{$flightId}", $newFlight);
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['errors']['flight'], 'Flight capacity is less than currently reserved seats');
        $this->assertDatabaseHas('flights', [
            'id'        => $flightId,
            'route_id'  => $flight[0]['route_id'],
            'capacity'  => $flight[0]['capacity'],
            'reserved'  => $flight[0]['reserved'],
            'price'     => $flight[0]['price'],
            'schedule'  => $flight[0]['schedule'],
        ]);
    }

    public function test_feature_fail_admin_edit_flight_reserved_less_than_currently_reserved()
    {
        User::factory(5)->create();

        $flight = Flight::factory(1)->create([
            'capacity' => 200,
            'reserved' => 150,
        ]);
        $flightId = $flight[0]->id;
        $passengers = rand(1, $flight[0]->capacity);
        
        Booking::factory(1)->create([
            'flight_id' => $flightId,
            'payable'   => $passengers * $flight[0]->price
        ]);

        $newReserved = rand(1, $passengers);
        $newFlight = Flight::factory(1)->make([
            'capacity' => $flight[0]->capacity, 
            'reserved' => $newReserved,
        ]);
        $newFlight[0]->schedule = Carbon::parse($newFlight[0]->schedule)->format('Y-m-d H:i');
        $newFlight = $newFlight[0]->toArray();

        $response = $this->actingAs($this->admin)->put("/api/admin/flight/{$flightId}", $newFlight);
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['errors']['flight'], 'Flight reserved is less than currently reserved seats');
        $this->assertDatabaseHas('flights', [
            'id'        => $flightId,
            'route_id'  => $flight[0]['route_id'],
            'capacity'  => $flight[0]['capacity'],
            'reserved'  => $flight[0]['reserved'],
            'price'     => $flight[0]['price'],
            'schedule'  => $flight[0]['schedule'],
        ]);
    }

    public function test_feature_succeed_admin_delete_flight()
    {
        $flight = Flight::factory(1)->create();

        $this->assertDatabaseCount('flights', 1);

        $flightId = $flight[0]->id;
        $response = $this->actingAs($this->admin)->delete("/api/admin/flight/{$flightId}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $this->assertSoftDeleted($flight[0]);
        $this->assertDatabaseCount('flights', 1);
    }

    public function test_feature_fail_admin_delete_flight_not_found()
    {
        $flight = Flight::factory(1)->create();

        $this->assertDatabaseCount('flights', 1);

        $flightId = $flight[0]->id;
        $flightId++;
        $response = $this->actingAs($this->admin)->delete("/api/admin/flight/{$flightId}");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $flightId--;
        $this->assertDatabaseHas('flights',[
            'id' => $flightId,
            'deleted_at' => null
        ]);
    }

    
    private function test_validation_errors(array $flight, String $function, String $invalidAttribute)
    {
        $this->expectException(ValidationException::class);
        $response = $this->actingAs($this->admin)->post("/api/admin/flight", $flight);

        log::info('top');
        $response->assertInvalid($invalidAttribute);
        $response->assertStatus(302);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        dd($response);
        log::info('bot');

        if($function === 'edit') {
            $this->assertDatabaseHas('flights', [
                'route_id'  => $flight['route_id'],
                'capacity'  => $flight['capacity'],
                'reserved'  => $flight['reserved'],
                'price'     => $flight['price'],
                'schedule'  => $flight['schedule']
            ]);
        }        
    }
}
