<?php

namespace App\Http\Requests;

use App\Models\Shop\Attribute;
use App\Models\Shop\Set;
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
                Rule::in(Set::all()->pluck('id')->toArray()),
            ],
            'name' => [
                'required',
            ],
            'slug' => [
                'required',
            ],
            'value' => [
                'required',
            ],
            'type' => [
                'required',
                'numeric',
                Rule::in(array_keys(Attribute::$types))
            ],
        ];
    }
}
