<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'route_id' => 'required|exists:routes,id',
            'capacity' => 'required|numeric',
            'reserved' => 'nullable|numeric|lte:capacity',
            'price' => 'required|numeric',
            'schedule' => 'required|date',
        ];
    }
}
