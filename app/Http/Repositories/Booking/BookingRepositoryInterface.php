<?php

namespace App\Http\Repositories\Booking;

interface BookingRepositoryInterface
{
    public function getAllBookings();
    public function getAllUserBookings();
    public function editBooking(array $data, String $id);
    public function deleteBooking(String $id);
}