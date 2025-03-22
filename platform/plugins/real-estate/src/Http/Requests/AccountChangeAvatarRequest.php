<?php

namespace Srapid\RealEstate\Http\Requests;

use Srapid\Support\Http\Requests\Request;

class AccountChangeAvatarRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     */
    public function rules()
    {
        return [
            'avatar' => 'required|image|mimes:jpg,jpeg,png',
        ];
    }
}
