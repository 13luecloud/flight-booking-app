<?php

namespace App\Exceptions;

use Exception;

class RouteSameOriginDestination extends Exception
{
    public function render($request)
    {
        return response()->error(
            'The given data is invalid', 
            ['route' => 'Route origin and destination are the same'], 
            422
        );            
    }
}
