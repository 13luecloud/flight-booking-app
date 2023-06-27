<?php

namespace App\Exceptions;

use Exception;

class CityExistsException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'The given data is invalid',
            'errors' => [
                'city' => 'City already exists'
            ]            
        ], 422);
    }
}
