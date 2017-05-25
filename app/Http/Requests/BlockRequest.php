<?php

namespace App\Http\Requests;

use App\Models\Cms\Block;
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
        if ($this->isMethod('get')) {
            return [];
        }

        $model = null;

        if ($this->route('block')) {
            $model = $this->route('block');
        } elseif ($this->route('id')) {
            $model = Block::withDrafts()->withTrashed()->find($this->route('id'));
        }

        return [
            'name' => [
                'required',
                Rule::unique('blocks', 'name')->ignore($model && $model->exists ? $model->id : null)
            ],
            'type' => [
                'required',
            ],
        ];
    }
}
