<?php

namespace App\Http\Requests;

use App\Models\Shop\Attribute;
use Illuminate\Validation\Rule;

class ValueRequest extends Request
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
                Rule::in(Attribute::all()->pluck('id')->toArray()),
            ],
            'value' => [
                'required',
            ],
        ];
    }
}
