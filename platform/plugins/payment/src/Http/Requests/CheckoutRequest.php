<?php

namespace Srapid\Payment\Http\Requests;

use Srapid\Payment\Enums\PaymentMethodEnum;
use Srapid\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CheckoutRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'required|' . Rule::in(PaymentMethodEnum::values()),
            'amount'         => 'required|min:0',
        ];
    }
}
