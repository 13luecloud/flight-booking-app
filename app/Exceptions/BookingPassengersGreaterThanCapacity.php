<?php

namespace App\Exceptions;

use Exception;

class BookingPassengersGreaterThanCapacity extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'The given data is invalid',
            'errors' => [
                'booking' => 'Flight cannot accommodate this many additional passengers'
            ]            
        ], 422);
    }
}
