<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'status' => false,
            'error' => true,
            'message' => 'Invalid credentials'
        ], 401);
    }
}
