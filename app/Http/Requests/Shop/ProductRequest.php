<?php

namespace App\Http\Requests\Shop;

use App\Http\Requests\Request;
use App\Models\Shop\Product;
use Illuminate\Validation\Rule;

class ProductRequest extends Request
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

        if ($this->route('product')) {
            $model = $this->route('product');
        } elseif ($this->route('id')) {
            $model = Product::withDrafts()->withTrashed()->find($this->route('id'));
        }

        return [
            'category_id' => [
                'required',
                'numeric',
                Rule::exists('product_categories', 'id'),
            ],
            'currency_id' => [
                'required',
                'numeric',
                Rule::exists('currencies', 'id'),
            ],
            'sku' => [
                'required',
                Rule::unique('products', 'sku')
                    ->ignore($model && $model->exists ? $model->id : null),
            ],
            'name' => [
                'required',
                Rule::unique('products', 'name')
                    ->ignore($model && $model->exists ? $model->id : null),
            ],
            'slug' => [
                'required',
                Rule::unique('products', 'slug')
                    ->ignore($model && $model->exists ? $model->id : null),
            ],
            'price' => [
                'required',
                'numeric',
            ],
            'quantity' => [
                'required',
                'numeric',
            ],
            'active' => [
                'required',
                Rule::in(array_keys(Product::$actives)),
            ],
        ];
    }

    /**
     * Get the pretty name of attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'category_id' => 'category',
            'currency_id' => 'currency',
        ];
    }
}
