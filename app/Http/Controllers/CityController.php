<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\CityStoreRequest; 
use App\Http\Requests\CityUpdateRequest;
use App\Http\Repositories\City\CityRepositoryInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends Controller
{
    private $repository; 
    public function __construct(CityRepositoryInterface $repository)
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
        return response()->success('Successfully retrieved all cities', $this->repository->getAllCities());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityStoreRequest $request)
    {
        $data = $this->repository->createCity($request->validated());
        return response()->success('Successfully created city', $data);
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
    public function update(CityUpdateRequest $request, $id)
    {
        return response()->success('Successfully updated city', $this->repository->editCity($request->validated(), $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->success('Successfully deleted City', $this->repository->deleteCity($id));
    }
}
