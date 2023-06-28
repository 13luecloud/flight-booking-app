<?php

namespace App\Exceptions;

use Exception;

class FlightLessThanReserved extends Exception
{
    protected $attribute;

    public function __construct($attribute)
    {
        $this->attribute = $attribute;
    }

    public function render($request)
    {
        return response()->error(
            'The given data is invalid', 
            ['flight' => 'Flight ' . $this->attribute . ' is less than currently reserved seats'], 
            422
        );            
    }
}
