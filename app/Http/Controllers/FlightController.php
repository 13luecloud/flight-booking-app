<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Flight\FlightRepositoryInterface;
use App\Http\Requests\FlightStoreRequest; 

use Illuminate\Http\Request;

class FlightController extends Controller
{
    private $repository; 
    public function __construct(FlightRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->success('Successfully retrieved flights', $this->repository->getAllFlights());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\FlightStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FlightStoreRequest $request)
    {
        return response()->success('Successfully created flight', $this->repository->createFlight($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
