<?php

namespace App\Http\Requests;

use App\Models\Shop\Discount;
use Illuminate\Validation\Rule;

class DiscountRequest extends Request
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
                Rule::unique('discounts', 'name')
                    ->ignore($this->route('discount') ? $this->route('discount')->id : null)
            ],
            'rate' => [
                'required',
                'numeric',
            ],
            'type' => [
                'required',
                Rule::in(array_keys(Discount::$types))
            ],
            'uses' => [
                'nullable',
                'numeric',
            ],
            'for' => [
                'required',
                Rule::in(array_keys(Discount::$for))
            ],
            'active' => [
                'required',
                Rule::in(array_keys(Discount::$actives))
            ],
            'start_date' => [
                'nullable',
                'before:end_date',
            ],
            'min_val' => [
                'nullable',
                'numeric',
            ]
        ];
    }
}
