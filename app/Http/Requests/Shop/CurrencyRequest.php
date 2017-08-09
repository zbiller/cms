<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CurrencyRequest extends Request
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
                Rule::unique('currencies', 'name')
                    ->ignore($this->route('currency') ? $this->route('currency')->id : null)
            ],
            'code' => [
                'required',
                Rule::unique('currencies', 'code')
                    ->ignore($this->route('currency') ? $this->route('currency')->id : null)
            ],
            'exchange_rate' => [
                'nullable',
                'numeric',
            ],
        ];
    }
}
