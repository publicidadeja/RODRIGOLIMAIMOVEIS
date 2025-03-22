<?php

namespace Srapid\Contact\Http\Requests;

use Srapid\Support\Http\Requests\Request;

class ContactReplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => 'required',
        ];
    }
}
