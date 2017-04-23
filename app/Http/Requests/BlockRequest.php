<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class BlockRequest extends Request
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
                Rule::unique('blocks', 'name')
                    ->ignore($this->route('block') ? $this->route('block')->id : null)
            ],
            'type' => [
                'required',
            ],
        ];
    }
}
