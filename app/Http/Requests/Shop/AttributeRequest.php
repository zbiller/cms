<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AttributeRequest extends Request
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
            'set_id' => [
                'required',
                'numeric',
                Rule::exists('attribute_sets', 'id'),
            ],
            'name' => [
                'required',
            ],
            'slug' => [
                'required',
            ],
        ];
    }

    /**
     * Get the pretty name of attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'set_id' => 'attribute set',
        ];
    }
}
