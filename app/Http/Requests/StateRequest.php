<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StateRequest extends Request
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
            'country_id' => [
                'required',
            ],
            'name' => [
                'required',
                Rule::unique('states', 'name')
                    ->ignore($this->route('state') ? $this->route('state')->id : null)
            ],
            'code' => [
                'required',
                Rule::unique('states', 'code')
                    ->ignore($this->route('state') ? $this->route('state')->id : null)
            ],
        ];
    }
}
