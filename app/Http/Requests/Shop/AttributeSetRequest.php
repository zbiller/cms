<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AttributeSetRequest extends Request
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
            'name' => [
                'required',
                Rule::unique('attribute_sets', 'name')
                    ->ignore($this->route('set') ? $this->route('set')->id : null),
            ],
            'slug' => [
                'required',
                Rule::unique('attribute_sets', 'slug')
                    ->ignore($this->route('set') ? $this->route('set')->id : null),
            ],
        ];
    }
}
