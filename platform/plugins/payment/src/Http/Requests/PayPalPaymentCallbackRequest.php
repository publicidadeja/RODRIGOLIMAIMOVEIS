<?php

namespace Srapid\Payment\Http\Requests;

use Srapid\Support\Http\Requests\Request;

class PayPalPaymentCallbackRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount'   => 'required|numeric',
            'currency' => 'required',
        ];
    }
}
