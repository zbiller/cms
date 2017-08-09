<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CityRequest extends Request
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
                Rule::exists('countries', 'id'),
            ],
            'name' => [
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
            'country_id' => 'country',
        ];
    }
}
