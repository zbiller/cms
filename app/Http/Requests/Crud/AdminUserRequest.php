<?php

namespace App\Http\Requests\Crud;

use App\Http\Requests\Request;

class AdminUserRequest extends Request
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
        $rules = [
            'username' => 'required|unique:users,username,' . $this->route('id'),
            'roles' => 'required|array|exists:roles,id',
            'person.first_name' => 'required|min:3',
            'person.last_name' => 'required|min:3',
            'person.email' => 'required|email|unique:persons,email,' . $this->route('id') . ',user_id',
        ];

        switch ($this->method()) {
            case 'POST':
                $rules['password'] = 'required|confirmed';
                break;
            case 'PUT':
                $rules['password'] = 'confirmed';
                break;
        }

        return $rules;
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
