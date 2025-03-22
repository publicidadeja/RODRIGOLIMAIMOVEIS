<?php

namespace Srapid\Payment\Http\Requests;

use Srapid\Payment\Enums\PaymentStatusEnum;
use Srapid\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => Rule::in(PaymentStatusEnum::values()),
        ];
    }
}
