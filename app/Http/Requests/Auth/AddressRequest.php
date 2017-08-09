<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\Auth\User;
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
                Rule::exists('users', 'id')->where('type', User::TYPE_FRONT),
            ],
            'country_id' => [
                'required',
                'numeric',
                Rule::exists('countries', 'id'),
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
            'user_id' => 'user',
            'country_id' => 'country',
            'state_id' => 'state',
            'city_id' => 'city',
        ];
    }
}
