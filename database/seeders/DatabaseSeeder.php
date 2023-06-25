<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use App\Models\City;
use App\Models\Route;
use App\Models\Flight;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(5)->create();
        User::factory(5)->create(['role' => 'admin']);
        City::factory(5)->create();
        Route::factory(5)->create();
        Flight::factory(7)->create();
    }
}
