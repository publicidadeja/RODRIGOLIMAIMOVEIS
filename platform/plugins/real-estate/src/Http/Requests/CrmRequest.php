<?php

namespace Srapid\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrmRequest extends FormRequest
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
            'name'          => 'required|string|max:120',
            'email'         => 'nullable|email|max:60',
            'phone'         => 'nullable|string|max:15',
            'content'       => 'nullable|string|max:400',
            'category'      => 'nullable|string',
            'lead_color'    => 'nullable|string',
            'property_value'=> 'nullable|string',
            'min_price'     => 'nullable|string',
            'max_price'     => 'nullable|string',
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Não validamos como numeric no rules() porque os valores chegam formatados com , e .
        // A validação numérica acontece no controller onde os valores são convertidos
    }
}