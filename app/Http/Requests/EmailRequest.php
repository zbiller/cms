<?php

namespace App\Http\Requests;

use App\Models\Cms\Email;
use App\Models\Cms\Page;
use Illuminate\Validation\Rule;

class EmailRequest extends Request
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
        if ($this->isMethod('get')) {
            return [];
        }

        $model = null;

        if ($this->route('email')) {
            $model = $this->route('email');
        } elseif ($this->route('id')) {
            $model = Email::withDrafts()->withTrashed()->find($this->route('id'));
        }

        return [
            'type' => [
                'required',
                'numeric',
            ],
            'name' => [
                'required',
                'min:3',
                Rule::unique('emails', 'name')->ignore($model && $model->exists ? $model->id : null),
            ],
            'identifier' => [
                $this->get('identifier') !== null ?
                    Rule::unique('emails', 'identifier')->ignore($model && $model->exists ? $model->id : null) :
                    null
            ],
        ];
    }
}
