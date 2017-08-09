<?php

namespace App\Http\Requests\Cms;

use App\Http\Requests\Request;
use App\Models\Cms\Layout;
use Illuminate\Validation\Rule;

class LayoutRequest extends Request
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
                Rule::unique('layouts', 'name')
                    ->ignore($this->route('layout') ? $this->route('layout')->id : null)
            ],
            'identifier' => [
                'nullable',
                Rule::unique('layouts', 'identifier')
                    ->ignore($this->route('layout') ? $this->route('layout')->id : null),
            ],
            'type' => [
                'required',
                Rule::in(array_keys(Layout::$types))
            ],
        ];
    }
}
