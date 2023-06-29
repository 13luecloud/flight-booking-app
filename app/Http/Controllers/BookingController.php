<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Booking\BookingRepositoryInterface; 
use App\Http\Requests\BookingEditRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    private $repository; 
    public function __construct(BookingRepositoryInterface $repository)
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
        //
    }

    public function indexUserBookings()
    {
        return response()->success('Successfully retrieved all user bookings', $this->repository->getAllUserBookings());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @param  App\Http\Requests\BookingEditRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookingEditRequest $request, $id)
    {
        return response()->success('Successfully edited booking', $this->repository->editBooking($request->validated(), $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  String  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        log::info($id);
        return response()->success('Successfully deleted booking', $this->repository->deleteBooking($id));
    }
}
