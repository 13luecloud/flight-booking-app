<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Route\RouteRepositoryInterface;
use App\Http\Requests\RouteRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RouteController extends Controller
{
    private $repository; 
    public function __construct(RouteRepositoryInterface $repository)
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
        return response()->success('Successfully retrieved all routes', $this->repository->getAllRoutes());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RouteRequest $request)
    {
        return response()->success('Successfully created route', $this->repository->createRoute($request->validated()));
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
    public function update(RouteRequest $request, $id)
    {
        $data = $this->repository->editRoute($request->validated(), $id);

        if(!$data) {
            return response()->error('Object not found', ['route' => 'Route does not exists'], 404);
        }
            return response()->success('Successfully updated route', $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->repository->deleteRoute($id);

        if(!$data) {
            return response()->error('Object not found', ['route' => 'Route does not exists'], 404);
        }
            return response()->success('Successfully deleted route', $data);
    }
}
