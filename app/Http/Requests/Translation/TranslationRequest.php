<?php

namespace App\Http\Requests\Translation;

use App\Http\Requests\Request;

class TranslationRequest extends Request
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
            'locale' => [
                'sometimes',
                'required',
                'min:2',
            ],
            'group' => [
                'sometimes',
                'required',
            ],
            'key' => [
                'sometimes',
                'required',
            ],
            'value' => [
                'required',
            ],
        ];
    }
}
