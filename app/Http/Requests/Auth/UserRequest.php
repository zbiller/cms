<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UserRequest extends Request
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
                    ->ignore($this->route('user') ? $this->route('user')->id : null)
            ],
            'password' => [
                'confirmed',
                $this->isMethod('post') ? 'required' : null
            ],
            'person.email' => [
                'required',
                'email',
                Rule::unique('people', 'email')
                    ->ignore($this->route('user') ? $this->route('user')->id : null, 'user_id')
            ],
            'person.first_name' => [
                'required',
                'min:3',
            ],
            'person.last_name' => [
                'required',
                'min:3',
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
