<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Http\Repositories\City\CityRepository;
use App\Models\City; 

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class CityTest extends TestCase
{
    use RefreshDatabase;

    private $repository;
    public function setUp(): void 
    {
        parent::setUp();
        $this->repository = app(CityRepository::class);
    }

    public function test_unit_succeeds_get_all_cities()
    {
        $createdCities = City::factory(3)->create();
        $fetchedCities = $this->repository->getAllCities();

        $this->assertNotEmpty($fetchedCities);
        $this->assertDatabaseCount('cities', 3); 

        for($i=0; $i < 3; $i++) {
            $this->assertModelExists($createdCities[$i]);

            $this->assertEquals($createdCities[$i]->id, $fetchedCities[$i]->id);
            $this->assertEquals($createdCities[$i]->name, $fetchedCities[$i]->name);
            $this->assertEquals($createdCities[$i]->code, $fetchedCities[$i]->code);
        }
    }

    public function test_unit_succeeds_create_cities()
    {
        $count = 3;
        $cities = City::factory($count)->make();

        $this->assertDatabaseCount('cities', 0);

        foreach($cities as $city) {
            $createdCity = $this->repository->createCity($city->toArray());
            
            $this->assertDatabaseHas('cities', [
                'name' => $city->name,
                'code' => $city->code
            ]);

            $this->assertInstanceOf(City::class, $createdCity);
            $this->assertEquals($city['name'], $createdCity->name);
            $this->assertEquals($city['code'], $createdCity->code);
        }

        $this->assertDatabaseCount('cities', $count);
    }

    public function test_unit_succeeds_edit_city()
    {
        $createdCities = City::factory(2)->create();
        $toEditCity = City::first();

        $newName = ['name' => 'New Name'];
        $newCity = $this->repository->editCity($newName, $toEditCity->id);

        $this->assertEquals($newName['name'], $newCity->name);
        $this->assertDatabaseHas('cities', [
            'name' => $newName['name']
        ]);
        $this->assertModelExists($newCity);
    }

    public function test_unit_succeeds_delete_city()
    {
        $createdCity = City::factory(1)->create();
        $cityId = $createdCity[0]->id;

        $this->assertDatabaseCount('cities', 1);

        $deletedCity = $this->repository->deleteCity($cityId);

        $this->assertDatabaseCount('cities', 1);
        $this->assertEquals($deletedCity->id, $cityId);
        $this->assertSoftDeleted($createdCity[0]);

    }
}
