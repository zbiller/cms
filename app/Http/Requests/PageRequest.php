<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PageRequest extends Request
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
            'layout_id' => [
                'required',
                'numeric'
            ],
            'type' => [
                'required',
                'numeric'
            ],
            'name' => [
                'required',
                'min:3'
            ],
            'slug' => [
                'required',
                Rule::unique('pages', 'slug')
                    ->ignore($this->route('page') ? $this->route('page')->id : null)
            ],
            'identifier' => [
                'required',
                Rule::unique('pages', 'identifier')
                    ->ignore($this->route('page') ? $this->route('page')->id : null)
            ],
        ];
    }
}
