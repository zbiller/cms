<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RoleRequest extends Request
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
                Rule::unique('roles', 'name')
                    ->ignore($this->route('role') ? $this->route('role')->id : null)
            ]
        ];
    }
}
