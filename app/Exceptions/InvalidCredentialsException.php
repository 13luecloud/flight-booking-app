<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'error' => true            
        ], 401);
    }
}
