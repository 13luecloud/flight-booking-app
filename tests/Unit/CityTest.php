<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

use App\Http\Repositories\City\CityRepository;
use App\Models\City; 

class CityTest extends TestCase
{
    use RefreshDatabase;

    private $repository;
    public function setUp(): void 
    {
        parent::setUp();
        $this->repository = app(CityRepository::class);
    }

    public function test_successfully_create_cities()
    {
        $cities = City::factory()->count(3)->make();
        foreach($cities as $city) {
            $createdCity = $this->repository->createCity($city->toArray());

            $this->assertInstanceOf(City::class, $createdCity);
            $this->assertEquals($city['name'], $createdCity->name);
            $this->assertEquals($city['code'], $createdCity->code);
        }
    }

}
