<?php

namespace App\Exceptions;

use Exception;

class FlightSameScheduleForRouteException extends Exception
{
    public function render($request)
    {
        return response()->error(
            'The given data is invalid', 
            ['flight' => 'Flight with the same route and schedule exists'], 
            422
        );            
    }
}
