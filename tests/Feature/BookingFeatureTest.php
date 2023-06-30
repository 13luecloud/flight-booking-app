<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\City;
use App\Models\Route;
use App\Models\Flight; 
use App\Models\Booking;

use Faker\Factory as faker;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Tests\TestCase;

class BookingFeatureTest extends TestCase
{
    Use RefreshDatabase; 
    use WithFaker;

    private User $admin; 
    private User $client;
    public function setUp(): void
    {
        parent::setUp();
        $admin = User::factory(1)->create(['role' => 'admin']);
        $this->admin = User::where('role', 'admin')->first();

        $client = User::factory(1)->create();
        $this->client = User::where('role', 'client')->first();

        City::factory(4)->create();
        Route::factory(3)->create();
        Flight::factory(4)->create();
    }

    public function test_feature_succeed_user_get_all_user_bookings()
    {
        $response = $this->actingAs($this->client)->get("/api/booking");

        $this->assertDatabaseCount('bookings', 0);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['data'], []);

        Booking::factory(3)->create([
            'user_id' => $this->client->id
        ]); 

        $response = $this->actingAs($this->client)->get("/api/booking");
        $this->assertDatabaseCount('bookings', 3);
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
        $response = $this->actingAs($this->admin)->get("/api/admin/booking");

        $this->assertDatabaseCount('bookings', 0);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['data'], []);

        Booking::factory(5)->create([]); 

        $response = $this->actingAs($this->admin)->get("/api/admin/booking");
        $this->assertDatabaseCount('bookings', 5);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $responseBody = $response->json();
        $this->assertCount(5, $responseBody['data']);
    }

    public function test_feature_fail_admin_cannot_access_user_get_all_bookings_function()
    {
        $response = $this->actingAs($this->admin)->get("/api/booking");

        $this->expectException(HttpException::class);
        $response->assertStatus(403);
        $responseBody = $response->json();
        $this->assertEquals($responseBody['message'], '');
    }

    public function test_feature_succeed_user_create_booking()
    {
        $passengerCount = 5;
        $flight = Flight::factory(1)->create([
            'capacity' => $passengerCount * 10,
            'reserved' => $passengerCount,
        ]);

        $booking = $this->createBookingData($flight[0]->id, $passengerCount);

        $response = $this->actingAs($this->client)->post("/api/booking", $booking);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'data'
        ]);
        $this->assertDatabaseHas('bookings', [
            'flight_id' => $booking['flight_id'],
            'user_id'   => $this->client->id,
            'status'    => 'unpaid'
        ]);

        $booking = Booking::where('user_id', $this->client->id)->first();
        $this->assertDatabaseHas('tickets', [
            'booking_id' => $booking->id,
        ]);
        $this->assertDatabaseCount('tickets', $passengerCount);
    }

    public function test_feature_fail_user_create_booking_capacity_overload()
    {
        $passengerCount = 5;

        $flight = Flight::factory(1)->create([
            'capacity' => $passengerCount,
            'reserved' => $passengerCount
        ]);
        
        $booking = $this->createBookingData($flight[0]->id, $passengerCount);

        $response = $this->actingAs($this->client)->post("/api/booking", $booking);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $this->assertDatabaseMissing('bookings', [
            'flight_id' => $booking['flight_id'],
            'user_id'   => $this->client->id,
            'status'    => 'unpaid'
        ]);
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_feature_succeed_admin_delete_booking()
    {
        $bookings = Booking::factory(3)->create();

        foreach($bookings as $booking) {
            $response = $this->actingAs($this->admin)->delete("/api/admin/booking/$booking->id");
            
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'success', 
                'message', 
                'data'
            ]);
            $this->assertSoftDeleted($booking);
        }

        $this->assertDatabaseCount('bookings', 3);
    }

    public function test_feature_fail_admin_delete_booking_not_found()
    {
        $bookings = Booking::factory(1)->create();

        $this->assertDatabaseCount('bookings', 1);

        $bookingId = $this->generateOtherBookingId();        

        $response = $this->actingAs($this->admin)->delete("/api/admin/booking/$bookingId");

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success', 
            'message', 
            'errors'
        ]);
        $this->assertDatabaseMissing('bookings',[
            'id' => $bookingId,
            'deleted_at' => null
        ]);
    }

    private function createBookingData(int $flightId, int $passengerCount)
    {
        $passengers = [];
        for($i = 0; $i < $passengerCount; $i++) {
            $passengers[] = $this->faker->name;
        }

        $booking = [];
        $booking['flight_id'] = $flightId;
        $booking['passengers'] = $passengers;

        return $booking;
    }

    private function generateOtherBookingId()
    {
        $id = 'B' . strtoupper(Str::random(8));
        $hasDuplicate = Booking::find($id);

        while($hasDuplicate) {
            $id = 'B' . strtoupper(Str::random(8));
            $hasDuplicate = Booking::find($id);
        }

        return $id;
    }

}
