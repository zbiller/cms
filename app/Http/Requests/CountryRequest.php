<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CountryRequest extends Request
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
                Rule::unique('countries', 'name')
                    ->ignore($this->route('country') ? $this->route('country')->id : null)
            ],
            'code' => [
                'required',
                Rule::unique('countries', 'code')
                    ->ignore($this->route('country') ? $this->route('country')->id : null)
            ],
        ];
    }
}
