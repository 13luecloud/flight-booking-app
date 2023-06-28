<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Http\Repositories\Flight\FlightRepository; 
use App\Models\City;
use App\Models\Flight;
use App\Models\Route;

use Carbon\Carbon;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class FlightTest extends TestCase
{
    use RefreshDatabase; 

    private $repository;
    public function setUp(): void 
    {
        parent::setUp();
        $this->repository = app(FlightRepository::class);

        City::factory(5)->create();
        Route::factory(4)->create();
    }

    public function test_unit_succeed_get_all_flights()
    {
        Flight::factory(5)->create();

        $flights = $this->repository->getAllFlights();
        $this->assertNotEmpty($flights);
        $this->assertDatabaseCount('flights', 5); 
        foreach($flights as $flight) {
            $this->assertModelExists($flight);
        }    
    }

    public function test_unit_succeed_create_flights()
    {
        $flights = Flight::factory(5)->make();
        
        $this->assertDatabaseCount('flights', 0);
        foreach($flights as $flight) {
            // DateTime to String, because $flight will be converted to array
            $flight->schedule = Carbon::parse($flight->schedule)->format('Y-m-d H:i');

            $createdFlight = $this->repository->createFlight($flight->toArray());

            $this->assertDatabaseHas('flights', [
                'route_id'  =>   $flight->route_id,
                'capacity'  =>   $flight->capacity,
                'reserved'  =>   $flight->reserved, 
                'price'     =>   $flight->price, 
                'schedule'  =>   $flight->schedule
            ]);

            // String to DateTime
            $flight->schedule = Carbon::createFromFormat('Y-m-d H:i', $flight->schedule);

            $this->assertEquals($createdFlight->route_id, $flight->route_id);
            $this->assertEquals($createdFlight->capacity, $flight->capacity);
            $this->assertEquals($createdFlight->reserved, $flight->reserved);
            $this->assertEquals($createdFlight->price, $flight->price);
            $this->assertEquals($createdFlight->schedule, $flight->schedule);
        }
    }

    public function test_unit_succeed_edit_flight()
    {
        $count = 3;
        $flights = Flight::factory($count)->create();
        $newFlights = Flight::factory($count)->make();
    
        foreach($newFlights as $flight) {  
            $toUpdateFlightId = Flight::inRandomOrder()->first()->id;          
            $flight->schedule = Carbon::parse($flight->schedule)->format('Y-m-d H:i');
            
            $updatedFlight = $this->repository->editFlight($flight->toArray(), $toUpdateFlightId);

            $this->assertDatabaseHas('flights', [
                'route_id'  =>   $flight->route_id,
                'capacity'  =>   $flight->capacity,
                'reserved'  =>   $flight->reserved, 
                'price'     =>   $flight->price, 
                'schedule'  =>   $flight->schedule
            ]);
            $this->assertModelExists($updatedFlight);
        }
    }

    public function test_unit_succeed_delete_flight()
    {
        $flights = Flight::factory(5)->create();

        foreach($flights as $flight) {
            $deletedFlight = $this->repository->deleteFlight($flight->id);

            //DateTime to String
            $flight->schedule = Carbon::parse($flight->schedule)->format('Y-m-d H:i:s');

            $this->assertEquals($deletedFlight->route_id, $flight->route_id);
            $this->assertEquals($deletedFlight->capacity, $flight->capacity);
            $this->assertEquals($deletedFlight->reserved, $flight->reserved);
            $this->assertEquals($deletedFlight->price, $flight->price);
            $this->assertEquals($deletedFlight->schedule, $flight->schedule);

            $this->assertSoftDeleted($flight);
        }
    }
}
