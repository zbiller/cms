<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RegisterRequest extends Request
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
            'username' => [
                'required',
                Rule::unique('users', 'username')
            ],
            'password' => [
                'required',
                'confirmed',
            ],
            'person.first_name' => [
                'required',
                'min:3'
            ],
            'person.last_name' => [
                'required',
                'min:3'
            ],
            'person.email' => [
                'required',
                'email',
                Rule::unique('persons', 'email')
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
            'person.first_name' => 'first name',
            'person.last_name' => 'last name',
            'person.email' => 'email',
        ];
    }
}
