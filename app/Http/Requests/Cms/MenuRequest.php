<?php

namespace App\Http\Requests\Cms;

use App\Http\Requests\Request;
use App\Models\Cms\Menu;
use Illuminate\Validation\Rule;

class MenuRequest extends Request
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
            ],
            'type' => [
                'required',
                Rule::in(array_keys(Menu::$types)),
            ],
        ];
    }
}
