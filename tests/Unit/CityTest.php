<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function test_succeeds_create_cities()
    {
        $cities = City::factory(3)->make();
        foreach($cities as $city) {
            $createdCity = $this->repository->createCity($city->toArray());

            $this->assertInstanceOf(City::class, $createdCity);
            $this->assertEquals($city['name'], $createdCity->name);
            $this->assertEquals($city['code'], $createdCity->code);
        }
    }

    public function test_succeeds_get_all_cities()
    {
        $count = 3;

        $createdCities = City::factory($count)->create();
        $fetchedCities = $this->repository->getAllCities();

        for($i=0; $i < $count; $i++) {
            $this->assertEquals($createdCities[$i]->id, $fetchedCities[$i]->id);
            $this->assertEquals($createdCities[$i]->name, $fetchedCities[$i]->name);
            $this->assertEquals($createdCities[$i]->code, $fetchedCities[$i]->code);
        }
    }

    public function test_succeeds_edit_city()
    {
        $createdCities = City::factory(2)->create();
        $toEditCity = City::first();

        $newName = ['name' => 'New Name'];
        $newCity = $this->repository->editCity($newName, $toEditCity->id);

        $this->assertEquals($newName['name'], $newCity->name);
    }

    public function test_fails_edit_city_cannot_find_object()
    {
        $count = 2;
        $createdCities = City::factory($count)->create();
        
        $newName = ['name' => 'New Name'];

        $this->expectException(ModelNotFoundException::class);
        $newCity = $this->repository->editCity($newName, $count+1);
        // Why is this throwing a green assertion when I don't have any assertions???????? 
        
        // $this->assertStatus(404);
        // $this->assertJson($newCity, $strict = false);        
    }
}
