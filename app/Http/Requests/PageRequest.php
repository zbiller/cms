<?php

namespace App\Http\Requests;

use App\Models\Cms\Page;
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
        if ($this->isMethod('get')) {
            return [];
        }

        $model = null;

        if ($this->route('page')) {
            $model = $this->route('page');
        } elseif ($this->route('id')) {
            $model = Page::withDrafts()->withTrashed()->find($this->route('id'));
        }

        return [
            'layout_id' => [
                'required',
                'numeric',
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
                Rule::unique('pages', 'slug')->ignore($model && $model->exists ? $model->id : null)
            ],
            'identifier' => [
                $this->get('identifier') !== null ?
                    Rule::unique('pages', 'identifier')->ignore($model && $model->exists ? $model->id : null) :
                    null
            ],
        ];
    }
}
