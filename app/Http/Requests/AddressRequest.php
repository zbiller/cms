<?php

namespace App\Http\Requests;

use App\Models\Auth\User;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Location\City;
use Illuminate\Validation\Rule;

class AddressRequest extends Request
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
            'user_id' => [
                'required',
                'numeric',
                Rule::in(User::type(User::TYPE_FRONT)->get()->pluck('id')->toArray()),
            ],
            'country_id' => [
                'required',
                'numeric',
                Rule::in(Country::all()->pluck('id')->toArray()),
            ],
            'state_id' => [
                'nullable',
                'numeric',
            ],
            'city_id' => [
                'nullable',
                'numeric',
            ],
            'address' => [
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
            'state_id' => 'state',
            'city_id' => 'city',
        ];
    }
}
