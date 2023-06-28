<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Flight\FlightRepositoryInterface;
use App\Http\Requests\FlightRequest; 

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
     * @param  \Illuminate\Http\FlightRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FlightRequest $request)
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
     * @param  \Illuminate\Http\FlightRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FlightRequest $request, $id)
    {
        return response()->success('Successfully updated flight', $this->repository->editFlight($request->validated(), $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->success('Successfully deleted flight', $this->repository->deleteFlight($id));
    }
}
