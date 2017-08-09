<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AttributeValueRequest extends Request
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
            'attribute_id' => [
                'required',
                'numeric',
                Rule::exists('attributes', 'id'),
            ],
            'value' => [
                'required',
            ],
        ];
    }
}
