<?php

namespace App\Http\Requests\Localisation;

use App\Http\Requests\Request;
use App\Models\Localisation\Language;
use Illuminate\Validation\Rule;

class LanguageRequest extends Request
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
                Rule::unique('languages', 'name')
                    ->ignore($this->route('language') ? $this->route('language')->id : null)
            ],
            'code' => [
                'required',
                Rule::unique('languages', 'code')
                    ->ignore($this->route('language') ? $this->route('language')->id : null)
            ],
            'default' => [
                'numeric',
                Rule::in(array_keys(Language::$defaults))
            ],
        ];
    }
}
