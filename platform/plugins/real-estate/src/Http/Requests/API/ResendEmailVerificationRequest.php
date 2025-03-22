<?php

namespace Srapid\RealEstate\Http\Requests\API;

use Srapid\Support\Http\Requests\Request;

class ResendEmailVerificationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|string',
        ];
    }
}
