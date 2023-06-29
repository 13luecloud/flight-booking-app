<?php

namespace App\Http\Repositories\Booking;

interface BookingRepositoryInterface
{
    public function editBooking(array $data, String $id);
    public function deleteBooking(String $id);
}