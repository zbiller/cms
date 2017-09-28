<?php

namespace App\Http\Requests\Seo;

use App\Http\Requests\Request;
use App\Models\Seo\Redirect;
use Illuminate\Validation\Rule;

class RedirectRequest extends Request
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
            'old_url' => [
                'required',
                Rule::unique('redirects', 'old_url')
                    ->ignore($this->route('redirect') ? $this->route('redirect')->id : null)
            ],
            'status' => [
                'numeric',
                Rule::in(array_keys(Redirect::$statuses))
            ],
        ];
    }
}
