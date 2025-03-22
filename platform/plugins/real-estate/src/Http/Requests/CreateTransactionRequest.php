<?php

namespace Srapid\RealEstate\Http\Requests;

use Srapid\RealEstate\Enums\TransactionTypeEnum;
use Srapid\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CreateTransactionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'credits' => 'required|numeric|min:1',
            'type'    => Rule::in(TransactionTypeEnum::values()),
        ];
    }
}
