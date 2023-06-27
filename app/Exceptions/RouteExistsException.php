<?php

namespace App\Exceptions;

use Exception;

class RouteExistsException extends Exception
{
    public function render($request)
    {
        return response()->error(
            'The given data is invalid', 
            ['route' => 'Route already exists'], 
            422
        );            
    }
}
