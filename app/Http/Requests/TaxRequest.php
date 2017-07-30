<?php

namespace App\Http\Requests;

use App\Models\Shop\Tax;
use Illuminate\Validation\Rule;

class TaxRequest extends Request
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
                Rule::unique('taxes', 'name')
                    ->ignore($this->route('tax') ? $this->route('tax')->id : null)
            ],
            'rate' => [
                'required',
                'numeric',
            ],
            'type' => [
                'required',
                Rule::in(array_keys(Tax::$types))
            ],
            'uses' => [
                'nullable',
                'numeric',
            ],
            'for' => [
                'required',
                Rule::in(array_keys(Tax::$for))
            ],
            'active' => [
                'required',
                Rule::in(array_keys(Tax::$actives))
            ],
            'start_date' => [
                'nullable',
                'before:end_date',
            ],
            'max_val' => [
                'nullable',
                'numeric',
            ]
        ];
    }
}
