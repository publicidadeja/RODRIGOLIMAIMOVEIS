<?php

namespace Srapid\RealEstate\Http\Requests;

use Srapid\Support\Http\Requests\Request;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|string',
            'password' => 'required|string',
        ];
    }
}
