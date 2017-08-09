<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\Request;
use App\Models\Shop\Category;
use Illuminate\Validation\Rule;

class CategoryRequest extends Request
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
        $model = null;

        if ($this->route('category')) {
            $model = $this->route('category');
        } elseif ($this->route('id')) {
            $model = Category::withDrafts()->withTrashed()->find($this->route('id'));
        }

        return [
            'name' => [
                'required',
                'min:3',
                Rule::unique('product_categories', 'name')
                    ->ignore($model && $model->exists ? $model->id : null),
            ],
            'slug' => [
                'required',
                Rule::unique('product_categories', 'slug')
                    ->ignore($model && $model->exists ? $model->id : null)
            ],
            'active' => [
                'required',
                Rule::in(array_keys(Category::$actives))
            ],
        ];
    }
}
